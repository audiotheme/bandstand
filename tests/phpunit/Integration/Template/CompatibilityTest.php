<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Compatibility;


class CompatibilityTest extends \WP_UnitTestCase {
	protected static $post;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$post = self::factory()->post->create( array(
			'post_status' => 'publish',
		) );
	}

	public function setUp() {
		parent::setUp();

		$this->plugin  = bandstand();
		$this->classname = 'Bandstand_Template_Compatibility';
		$this->instance = new Bandstand_Template_Compatibility();
	}

	public function test_enable_compatibility() {
		$this->instance->enable();

		$this->assertTrue( $this->instance->is_active() );
		$this->assertEquals( 10, has_action( 'loop_start', array( $this->instance, 'loop_start' ) ) );
	}

	public function test_set_loop_template_part() {
		$class = new \ReflectionClass( $this->classname );
		$this->instance->set_loop_template_part( 'video/loop', 'single' );

		$property = $class->getProperty( 'template_part_slug' );
		$property->setAccessible( true );
		$this->assertEquals( 'video/loop', $property->getValue( $this->instance ) );

		$property = $class->getProperty( 'template_part_name' );
		$property->setAccessible( true );
		$this->assertEquals( 'single', $property->getValue( $this->instance ) );
	}

	public function test_get_title() {
		$class = new \ReflectionClass( $this->classname );
		$method = new \ReflectionMethod( $this->classname, 'get_title' );
		$method->setAccessible( true );

		$this->assertEquals( '', $method->invoke( $this->instance ) );
	}

	public function test_get_title_on_archive() {
		$class = new \ReflectionClass( $this->classname );
		$method = new \ReflectionMethod( $this->classname, 'get_title' );
		$method->setAccessible( true );

		$category = self::factory()->term->create( array(
			'taxonomy' => 'category',
			'slug'     => 'foo',
			'name'     => 'Foo',
		) );

		$this->go_to( get_term_link( $category ) );

		$this->assertEquals( get_the_archive_title(), $method->invoke( $this->instance ) );

		$this->instance->set_title( 'Title' );
		$this->assertEquals( 'Title', $method->invoke( $this->instance ) );
	}

	public function test_loop_start() {
		$class = new \ReflectionClass( $this->classname );
		$this->go_to( get_permalink( self::$post ) );
		$this->instance->enable();
		$post = $GLOBALS['wp_the_query']->post;
		the_post();

		$property = $class->getProperty( 'the_query' );
		$property->setAccessible( true );
		$the_query = $property->getValue( $this->instance );

		$property = $class->getProperty( 'has_main_loop_started' );
		$property->setAccessible( true );

		$this->assertTrue( $property->getValue( $this->instance ) );
		$this->assertEquals( $post, $the_query->post );
		$this->assertEquals( 10, has_action( 'loop_end', array( $this->instance, 'loop_end' ) ) );
	}

	public function test_remove_and_restore_all_filters() {
		$class = new \ReflectionClass( $this->classname );
		$method = new \ReflectionMethod( $this->classname, 'remove_all_filters' );
		$method->setAccessible( true );

		add_filter( 'the_content', '__remove_empty_string' );
		$this->assertEquals( 10, has_filter( 'the_content', '__remove_empty_string' ) );

		$method->invoke( $this->instance, 'the_content' );
		$this->assertFalse( has_filter( 'the_content' ) );

		$method = new \ReflectionMethod( $this->classname, 'restore_all_filters' );
		$method->setAccessible( true );

		$method->invoke( $this->instance, 'the_content' );
		$this->assertEquals( 10, has_filter( 'the_content', '__remove_empty_string' ) );

		remove_filter( 'the_content', '__remove_empty_string' );
	}
}

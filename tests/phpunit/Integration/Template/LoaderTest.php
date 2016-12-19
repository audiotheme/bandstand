<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Loader;


class LoaderTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->plugin = bandstand();
		$this->classname = 'Bandstand_Template_Loader';
		$this->loader = new Bandstand_Template_Loader( $this->plugin );
	}

	public function test_plugin_template_directory_property() {
		$class = new \ReflectionClass( $this->classname );
		$property = $class->getProperty( 'plugin_template_directory' );
		$property->setAccessible( true );
		$this->assertEquals( 'templates', $property->getValue( $this->loader ) );
	}

	public function test_filter_prefix_property() {
		$class = new \ReflectionClass( $this->classname );
		$property = $class->getProperty( 'filter_prefix' );
		$property->setAccessible( true );
		$this->assertEquals( 'bandstand', $property->getValue( $this->loader ) );
	}
}

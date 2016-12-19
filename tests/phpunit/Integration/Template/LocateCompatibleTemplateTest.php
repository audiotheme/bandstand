<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Loader;

class LocateCompatibleTemplateTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->plugin  = bandstand();
		$this->manager = $this->plugin->templates;
	}

	public function test_locate_template() {
		$expected = get_template_directory() . '/page.php';
		$template = $this->manager->locate_template( 'archive-record.php' );

		$this->assertEquals( $expected, $template );
	}

	public function test_locate_template_with_theme_support() {
		add_theme_support( 'bandstand' );

		$expected = $this->plugin->get_path( 'templates/archive-record.php' );
		$template = $this->manager->locate_template( 'archive-record.php' );

		$this->assertEquals( $expected, $template );

		remove_theme_support( 'bandstand' );
	}
}

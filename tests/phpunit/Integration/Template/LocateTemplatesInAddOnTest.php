<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Loader;

class LocateTemplatesInAddOnTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		add_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );

		$this->plugin = bandstand();
		$this->loader = new Bandstand_Template_Loader( $this->plugin );
	}

	public function tearDown() {
		remove_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );
	}

	public function test_locate_template() {
		$template = $this->loader->locate_template( 'archive-record.php' );

		$expected = BANDSTAND_TESTS_DIR . '/data/plugins/template-pack/templates/archive-record.php';
		$this->assertEquals( $expected, $template );
	}

	public function _register_addon_templates_directory( $paths ) {
		$paths[5] = BANDSTAND_TESTS_DIR . '/data/plugins/template-pack/templates';
		return $paths;
	}
}

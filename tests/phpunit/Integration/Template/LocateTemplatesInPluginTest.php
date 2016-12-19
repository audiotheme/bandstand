<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Loader;

class LocateTemplatesInPluginTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->plugin = bandstand();
		$this->loader = $this->plugin->templates->loader;
	}

	public function test_locate_template() {
		$expected = $this->plugin->get_path( 'templates/archive-record.php' );
		$template = $this->loader->locate_template( 'archive-record.php' );

		$this->assertEquals( $expected, $template );
	}
}

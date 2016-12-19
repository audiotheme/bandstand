<?php
namespace Bandstand\Test\Integration\Template;

use Bandstand_Template_Loader;

class LocateTemplatesInThemesTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->theme_root = BANDSTAND_TESTS_DIR . '/data/themes';
		$this->original_stylesheet = get_stylesheet();
		$this->original_theme_directories = $GLOBALS['wp_theme_directories'];

		// /themes is necessary as theme.php functions assume /themes is the root if there is only one root.
		$GLOBALS['wp_theme_directories'] = array( WP_CONTENT_DIR . '/themes', $this->theme_root );

		add_filter( 'theme_root',      array( $this, '_theme_root' ) );
		add_filter( 'stylesheet_root', array( $this, '_theme_root' ) );
		add_filter( 'template_root',   array( $this, '_theme_root' ) );

		$this->plugin = bandstand();
		$this->loader = new Bandstand_Template_Loader( $this->plugin );
	}

	public function teardDown() {
		$GLOBALS['wp_theme_directories'] = $this->original_theme_directories;
		switch_theme( $this->original_stylesheet );

		remove_filter( 'theme_root',      array( $this, '_theme_root' ) );
		remove_filter( 'stylesheet_root', array( $this, '_theme_root' ) );
		remove_filter( 'template_root',   array( $this, '_theme_root' ) );

		parent::tearDown();
	}

	public function test_locate_template_in_parent_theme() {
		switch_theme( 'parent' );

		$expected = get_template_directory() . '/bandstand/archive-record.php';
		$template = $this->loader->locate_template( 'archive-record.php' );

		$this->assertEquals( $expected, $template );
	}

	public function test_locate_template_in_child_theme() {
		switch_theme( 'child' );

		$expected = get_stylesheet_directory() . '/bandstand/archive-record.php';
		$template = $this->loader->locate_template( 'archive-record.php' );

		$this->assertTrue( get_template() !== get_stylesheet() );
		$this->assertEquals( $expected, $template );
	}

	public function test_locate_template_in_parent_theme_with_addon() {
		switch_theme( 'parent' );

		add_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );

		$expected = get_template_directory() . '/bandstand/archive-record.php';
		$template = $this->loader->locate_template( 'archive-record.php' );

		$this->assertNotEquals( $expected, $template );

		remove_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );
	}

	public function test_locate_template_in_child_theme_with_addon() {
		switch_theme( 'child' );

		add_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );

		$expected = get_stylesheet_directory() . '/bandstand/archive-record.php';
		$template = $this->loader->locate_template( 'archive-record.php' );

		$this->assertTrue( get_template() !== get_stylesheet() );
		$this->assertEquals( $expected, $template );

		remove_filter( 'bandstand_template_paths', array( $this, '_register_addon_templates_directory' ) );
	}

	public function _register_addon_templates_directory( $paths ) {
		$paths[5] = BANDSTAND_TESTS_DIR . '/data/plugins/template-pack/templates';
		return $paths;
	}

	public function _theme_root( $directory ) {
		return $this->theme_root;
	}
}

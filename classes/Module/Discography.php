<?php
/**
 * Discography module.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Discography module class.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_Module_Discography extends Bandstand_Module_AbstractModule {
	/**
	 * Admin menu item HTML id.
	 *
	 * Used for hiding menu items when toggling modules.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $admin_menu_id = 'menu-posts-bandstand_record';

	/**
	 * Module id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $id = 'discography';

	/**
	 * Whether the module should show on the dashboard.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $show_in_dashboard = true;

	/**
	 * Retrieve the name of the module.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return esc_html__( 'Discography', 'bandstand' );
	}

	/**
	 * Retrieve the module description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description() {
		return esc_html__( 'Upload album artwork, assign titles and tracks, add audio files, and enter links to purchase your music.', 'bandstand' );
	}

	/**
	 * Load the module.
	 *
	 * @since 1.0.0
	 *
	 * @return $this
	 */
	public function load() {
		require( $this->plugin->get_path( 'includes/discography-template.php' ) );

		return $this;
	}

	/**
	 * Register module hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		$this->plugin->register_hooks( new Bandstand_Taxonomy_Genre( $this ) );
		$this->plugin->register_hooks( new Bandstand_Taxonomy_RecordType( $this ) );
		$this->plugin->register_hooks( new Bandstand_PostType_Playlist( $this ) );
		$this->plugin->register_hooks( new Bandstand_PostType_Record( $this ) );
		$this->plugin->register_hooks( new Bandstand_PostType_Track( $this ) );
		$this->plugin->register_hooks( new Bandstand_AJAX_Discography() );

		add_action( 'init',                              array( $this, 'register_archive' ), 20 );
		add_filter( 'bandstand_archive_settings_fields', array( $this, 'register_archive_settings_fields' ), 10, 2 );
		add_action( 'rest_api_init',                     array( $this, 'register_rest_routes' ) );
		add_filter( 'generate_rewrite_rules',            array( $this, 'generate_rewrite_rules' ) );
		add_action( 'template_include',                  array( $this, 'template_include' ) );

		if ( is_admin() ) {
			$this->plugin->register_hooks( new Bandstand_Screen_ManageRecords() );
			$this->plugin->register_hooks( new Bandstand_Screen_ManageTracks() );
			$this->plugin->register_hooks( new Bandstand_Screen_EditRecord() );
			$this->plugin->register_hooks( new Bandstand_Screen_EditTrack() );
			$this->plugin->register_hooks( new Bandstand_Screen_EditRecordArchive() );
		}
	}

	/**
	 * Register the discography archive.
	 *
	 * @since 1.0.0
	 */
	public function register_archive() {
		$this->plugin->modules['archives']->add_post_type_archive( 'bandstand_record' );
	}

	/**
	 * Activate default archive setting fields.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $fields    List of default fields to activate.
	 * @param  string $post_type Post type archive.
	 * @return array
	 */
	public function register_archive_settings_fields( $fields, $post_type ) {
		if ( ! in_array( $post_type, array( 'bandstand_record' ), true ) ) {
			return $fields;
		}

		$fields['columns'] = array(
			'choices' => range( 2, 4 ),
			'default' => 3,
		);

		$fields['order'] = true;
		$fields['posts_per_archive_page'] = true;

		return $fields;
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		$controller = new Bandstand_REST_RecordsController( 'bandstand/v1', 'records', 'bandstand_record' );
		$controller->register_routes();

		$controller = new Bandstand_REST_TracksController( 'bandstand/v1', 'tracks', 'bandstand_track' );
		$controller->register_routes();
	}

	/**
	 * Get the discography rewrite base. Defaults to 'music'.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_rewrite_base() {
		global $wp_rewrite;

		$front = '';
		$base  = get_option( 'bandstand_record_rewrite_base', 'music' );

		if ( $wp_rewrite->using_index_permalinks() ) {
			$front = $wp_rewrite->index . '/';
		}

		return $front . $base;
	}

	/**
	 * Display the module overview.
	 *
	 * @since 1.0.0
	 */
	public function display_overview() {
		?>
		<figure class="bandstand-module-card-overview-media">
			<iframe src="https://www.youtube.com/embed/ZopsZEiv1F0?rel=0" frameborder="0" allowfullscreen></iframe>
		</figure>
		<p>
			<?php esc_html_e( 'Everything you need to build your Discography is at your fingertips.', 'bandstand' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Your discography is the window through which listeners are introduced to and discover your music on the web. Encourage that discovery on your website through a detailed and organized history of your recorded output using the Bandstand discography feature. Upload album artwork, assign titles and tracks, add audio files, and enter links to purchase your music.', 'bandstand' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Try it out:', 'bandstand' ); ?></strong> <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=bandstand_record' ) ); ?>"><?php esc_html_e( 'Add a record', 'bandstand' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Display a button to perform the module's primary action.
	 *
	 * @since 1.0.0
	 */
	public function display_primary_button() {
		printf(
			'<a href="%s" class="button">%s</a>',
			esc_url( admin_url( 'post-new.php?post_type=bandstand_record' ) ),
			esc_html__( 'Add Record', 'bandstand' )
		);
	}

	/**
	 * Load discography templates.
	 *
	 * Templates should be included in an /bandstand/ directory within the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function template_include( $template ) {
		$manager = $this->plugin->templates;

		if ( is_post_type_archive( array( 'bandstand_record', 'bandstand_track' ) ) || is_tax( array( 'bandstand_genre', 'bandstand_record_type' ) ) ) {
			if ( is_post_type_archive( 'bandstand_track' ) ) {
				$templates[] = 'archive-track.php';
			}

			if ( is_tax() ) {
				$term = get_queried_object();
				$taxonomy = str_replace( 'bandstand_', '', $term->taxonomy );
				$templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
				$templates[] = "taxonomy-$taxonomy.php";
			}

			$templates[] = 'archive-record.php';

			$manager->set_title( $this->plugin->modules['archives']->get_archive_title() );
			$manager->set_loop_template_part( 'record/loop', 'archive' );
		} elseif ( is_singular( 'bandstand_record' ) ) {
			$templates[] = 'single-record.php';

			$manager->set_title( get_queried_object()->post_title );
			$manager->set_loop_template_part( 'record/loop', 'single' );
		} elseif ( is_singular( 'bandstand_track' ) ) {
			$templates[] = 'single-track.php';

			$manager->set_title( get_queried_object()->post_title );
			$manager->set_loop_template_part( 'track/loop', 'single' );
		}

		if ( ! empty( $templates ) ) {
			$template = $manager->locate_template( $templates );
			do_action( 'bandstand_template_include', $template );
		}

		return $template;
	}

	/**
	 * Add custom discography rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Rewrite $wp_rewrite The main rewrite object. Passed by reference.
	 */
	public function generate_rewrite_rules( $wp_rewrite ) {
		$base    = $this->get_rewrite_base();
		$tracks  = $this->get_tracks_rewrite_base();
		$archive = $this->get_tracks_archive_rewrite_base();

		$new_rules[ $base . '/' . $archive . '/?$' ] = 'index.php?post_type=bandstand_track';
		$new_rules[ $base . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$' ] = 'index.php?post_type=bandstand_record&paged=$matches[1]';
		$new_rules[ $base . '/([^/]+)/' . $tracks . '/([^/]+)?$' ] = 'index.php?bandstand_record=$matches[1]&bandstand_track=$matches[2]';
		$new_rules[ $base . '/([^/]+)/?$' ] = 'index.php?bandstand_record=$matches[1]';
		$new_rules[ $base . '/?$' ] = 'index.php?post_type=bandstand_record';

		$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );
	}

	/**
	 * Retrieve the base slug to use for the namespace in track rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_tracks_rewrite_base() {
		$slug = preg_replace( '/[^a-z0-9-_]/', '', _x( 'track', 'track permalink slug', 'bandstand' ) );

		if ( empty( $slug ) ) {
			$slug = 'track';
		}

		return apply_filters( 'bandstand_tracks_rewrite_base', $slug );
	}

	/**
	 * Retrieve the base slug to use for tracks archive rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_tracks_archive_rewrite_base() {
		$slug = preg_replace( '/[^a-z0-9-_]/', '', _x( 'tracks', 'tracks archive permalink slug', 'bandstand' ) );

		if ( empty( $slug ) ) {
			$slug = 'tracks';
		}

		return apply_filters( 'bandstand_tracks_archive_rewrite_base', $slug );
	}
}

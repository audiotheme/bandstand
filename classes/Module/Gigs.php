<?php
/**
 * Gigs module.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Gigs module class.
 *
 * @package Bandstand\Gigs
 * @since   1.0.0
 */
class Bandstand_Module_Gigs extends Bandstand_Module_AbstractModule {
	/**
	 * Admin menu item HTML id.
	 *
	 * Used for hiding menu items when toggling modules.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $admin_menu_id = 'menu-posts-bandstand_gig';

	/**
	 * Module id.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $id = 'gigs';

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
		return esc_html__( 'Gigs & Venues', 'bandstand' );
	}

	/**
	 * Retrieve the module description.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description() {
		return esc_html__( 'Share event details with your fans, including location, venue, date, time, and ticket prices.', 'bandstand' );
	}

	/**
	 * Load the module.
	 *
	 * @since 1.0.0
	 *
	 * @return $this
	 */
	public function load() {
		require( $this->plugin->get_path( 'includes/gig-template.php' ) );
		require( $this->plugin->get_path( 'includes/venue-template.php' ) );
		return $this;
	}

	/**
	 * Register module hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		$this->plugin->register_hooks( new Bandstand_PostType_Gig( $this ) );
		$this->plugin->register_hooks( new Bandstand_PostType_Venue() );

		add_action( 'init',                              array( $this, 'register_archive' ), 20 );
		add_filter( 'bandstand_archive_settings_fields', array( $this, 'register_archive_settings_fields' ), 10, 2 );
		add_action( 'rest_api_init',                     array( $this, 'register_rest_routes' ) );
		add_filter( 'generate_rewrite_rules',            array( $this, 'generate_rewrite_rules' ) );
		add_action( 'template_redirect',                 array( $this, 'template_redirect' ) );
		add_action( 'template_include',                  array( $this, 'template_include' ) );
		add_filter( 'the_posts',                         array( $this, 'query_connected_venues' ), 10, 2 );
		add_action( 'wp_footer',                         array( $this, 'maybe_print_upcoming_gigs_jsonld' ) );
		add_filter( 'wxr_export_skip_postmeta',          array( $this, 'exclude_meta_from_export' ), 10, 2 );
		add_action( 'import_end',                        array( $this, 'remap_gig_venues' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ), 1 );

			$this->plugin->register_hooks( new Bandstand_Screen_ManageGigs() );
			$this->plugin->register_hooks( new Bandstand_Screen_EditGig() );
			$this->plugin->register_hooks( new Bandstand_Screen_ManageVenues() );
			$this->plugin->register_hooks( new Bandstand_Screen_EditVenue() );
		}
	}

	/**
	 * Retrieve the Google Maps API key.
	 *
	 * On multisite, this defaults to a key saved for the blog, but will fall
	 * back to a global key if Bandstand is network activated.
	 *
	 * Always use the global key in the network admin panel.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_google_maps_api_key() {
		$option_name = Bandstand_Provider_Setting_GoogleMaps::API_KEY_OPTION_NAME;

		$value = get_option( $option_name, '' );
		if ( empty( $value ) || is_network_admin() ) {
			$value = get_site_option( $option_name, '' );
		}

		return $value;
	}

	/**
	 * Register the gig archive.
	 *
	 * @since 1.0.0
	 */
	public function register_archive() {
		$this->plugin->modules['archives']->add_post_type_archive( 'bandstand_gig' );
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
		if ( ! in_array( $post_type, array( 'bandstand_gig' ), true ) ) {
			return $fields;
		}

		$fields['posts_per_archive_page'] = true;

		return $fields;
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.0.0
	 */
	public function register_rest_routes() {
		$controller = new Bandstand_REST_GigsController( 'bandstand/v1', 'gigs', 'bandstand_gig' );
		$controller->register_routes();

		$controller = new Bandstand_REST_VenuesController( 'bandstand/v1', 'venues', 'bandstand_venue' );
		$controller->register_routes();
	}

	/**
	 * Get the gigs rewrite base. Defaults to 'shows'.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_rewrite_base() {
		global $wp_rewrite;

		$front = '';
		$base  = get_option( 'bandstand_gig_rewrite_base', 'shows' );

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
			<iframe src="https://www.youtube.com/embed/3ApVW-5MLLU?rel=0"></iframe>
		</figure>
		<p>
			<strong><?php esc_html_e( 'Keep fans updated with live performances, tour dates and venue information.', 'bandstand' ); ?></strong>
		</p>
		<p>
			<?php esc_html_e( "Schedule all the details about your next show, including location (address, city, state), dates, times, ticket prices and links to ticket purchasing. Set up your venue information by creating new venues and assigning shows to venues you've already created. You also have the ability to feature each venue's website, along with their contact information like email address and phone number.", 'bandstand' ); ?>
		</p>
		<p>
			<strong><?php esc_html_e( 'Try it out:', 'bandstand' ); ?></strong> <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=bandstand_gig' ) ); ?>"><?php esc_html_e( 'Add a gig', 'bandstand' ); ?></a>
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
			esc_url( admin_url( 'post-new.php?post_type=bandstand_gig' ) ),
			esc_html__( 'Add Gig', 'bandstand' )
		);
	}

	/**
	 * Reroute feed requests to the appropriate template for processing.
	 *
	 * @since 1.0.0
	 */
	public function template_redirect() {
		global $wp_query;

		if ( ! is_feed() || 'bandstand_gig' !== $wp_query->get( 'post_type' ) ) {
			return;
		}

		require( $this->plugin->get_path( 'includes/views/gig-feed.php' ) );

		$type = $wp_query->get( 'feed' );

		switch ( $type ) {
			case 'feed':
				load_template( $this->plugin->get_path( 'includes/views/gig-feed-rss2.php' ) );
				break;
			case 'ical':
				load_template( $this->plugin->get_path( 'includes/views/gig-feed-ical.php' ) );
				break;
			default:
				$message = sprintf( esc_html__( 'ERROR: %s is not a valid feed template.', 'bandstand' ), $type );
				wp_die( esc_html( $message ), '', array( 'response' => 404 ) );
		}
		exit;
	}

	/**
	 * Load gig templates.
	 *
	 * Templates should be included in a /bandstand/ directory within the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $template Template path.
	 * @return string
	 */
	public function template_include( $template ) {
		$manager = $this->plugin->templates;

		if ( is_post_type_archive( 'bandstand_gig' ) ) {
			$templates[] = 'archive-gig.php';

			$manager->set_title( $this->plugin->modules['archives']->get_archive_title() );
			$manager->set_loop_template_part( 'gig/loop', 'archive' );
		} elseif ( is_singular( 'bandstand_gig' ) ) {
			$templates[] = 'single-gig.php';

			$manager->set_title( get_queried_object()->post_title );
			$manager->set_loop_template_part( 'gig/loop', 'single' );
		}

		if ( ! empty( $templates ) ) {
			$template = $manager->locate_template( $templates );
			do_action( 'bandstand_template_include', $template );
		}

		return $template;
	}

	/**
	 * Prime the cache with venues connected to gigs in a query.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $posts Array of posts.
	 * @param WP_Query $wp_query Query passed by reference.
	 * @return array
	 */
	public function query_connected_venues( $posts, $wp_query ) {
		if ( ! empty( $posts ) && 'bandstand_gig' === get_post_type( $posts[0] ) ) {
			$venue_ids = wp_list_pluck( $posts, 'bandstand_venue_id' );
			_prime_post_caches( $venue_ids );
		}

		return $posts;
	}

	/**
	 * Add custom gig rewrite rules.
	 *
	 * /base/YYYY/MM/DD/(feed|ical)/
	 * /base/YYYY/MM/DD/
	 * /base/YYYY/MM/(feed|ical)/
	 * /base/YYYY/MM/
	 * /base/YYYY/(feed|ical)/
	 * /base/YYYY/
	 * /base/(feed|ical)/
	 * /base/%postname%/
	 * /base/
	 *
	 * @todo /base/tour/%tourname%/
	 *       /base/past/page/2/
	 *       /base/past/
	 *       /base/YYYY/page/2/
	 *       etc.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Rewrite $wp_rewrite The main rewrite object. Passed by reference.
	 */
	public function generate_rewrite_rules( $wp_rewrite ) {
		$base = $this->get_rewrite_base();
		$past = $this->get_past_rewrite_base();

		$new_rules[ $base . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|ical)/?$' ] = 'index.php?post_type=bandstand_gig&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]';
		$new_rules[ $base . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$' ] = 'index.php?post_type=bandstand_gig&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]';
		$new_rules[ $base . '/([0-9]{4})/([0-9]{1,2})/(feed|ical)/?$' ] = 'index.php?post_type=bandstand_gig&year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]';
		$new_rules[ $base . '/([0-9]{4})/([0-9]{1,2})/?$' ] = 'index.php?post_type=bandstand_gig&year=$matches[1]&monthnum=$matches[2]';
		$new_rules[ $base . '/([0-9]{4})/?$' ] = 'index.php?post_type=bandstand_gig&year=$matches[1]';
		$new_rules[ $base . '/(feed|ical)/?$' ] = 'index.php?post_type=bandstand_gig&feed=$matches[1]';
		$new_rules[ $base . '/' . $past . '/' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$' ] = 'index.php?post_type=bandstand_gig&paged=$matches[1]&bandstand_gig_range=past';
		$new_rules[ $base . '/' . $past . '/?$' ] = 'index.php?post_type=bandstand_gig&bandstand_gig_range=past';
		$new_rules[ $base . '/([^/]+)/(ical)/?$' ] = 'index.php?bandstand_gig=$matches[1]&feed=$matches[2]';
		$new_rules[ $base . '/([^/]+)/?$' ] = 'index.php?bandstand_gig=$matches[1]';
		$new_rules[ $base . '/?$' ] = 'index.php?post_type=bandstand_gig';

		$wp_rewrite->rules = array_merge( $new_rules, $wp_rewrite->rules );
	}

	/**
	 * Print a JSON-LD tag for upcoming gigs.
	 *
	 * @since 1.0.0
	 */
	 public function maybe_print_upcoming_gigs_jsonld() {
 		if ( ! is_post_type_archive( 'bandstand_gig' ) && ! is_front_page() ) {
 			return;
 		}

		$args = array(
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		);

		$wp_query = new Bandstand_Query_Gigs( $args );
		if ( ! empty( $wp_query->posts ) ) {
			$this->print_jsonld( $wp_query->posts );
		}
	}

	/**
	 * Register administration scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_assets() {
		$post_type_object = get_post_type_object( 'bandstand_venue' );
		$base_url = set_url_scheme( $this->plugin->get_url( 'admin/js' ) );

		wp_register_script(
			'bandstand-google-maps',
			add_query_arg( 'key', $this->get_google_maps_api_key(), 'https://maps.googleapis.com/maps/api/js?libraries=places' )
		);

		wp_register_script(
			'bandstand-gig-edit',
			$base_url . '/gig-edit.bundle.min.js',
			array( 'bandstand-admin', 'bandstand-google-maps', 'jquery-timepicker', 'jquery-ui-autocomplete', 'media-views', 'pikaday', 'wp-api', 'wp-backbone', 'wp-util' ),
			BANDSTAND_VERSION,
			true
		);

		wp_register_script(
			'bandstand-venue-edit',
			$base_url . '/venue-edit.bundle.min.js',
			array( 'bandstand-admin', 'bandstand-google-maps', 'jquery-ui-autocomplete', 'post', 'underscore', 'wp-api' ),
			BANDSTAND_VERSION,
			true
		);

		wp_register_style(
			'bandstand-venue-manager',
			$this->plugin->get_url( 'admin/css/venue-manager.min.css' ),
			array(),
			'1.0.0'
		);

		$settings = array(
			'defaultTimeZoneId' => get_option( 'timezone_string' ),
			'googleMapsApiKey'  => $this->get_google_maps_api_key(),
			'restUrl'           => esc_url_raw( get_rest_url() ),
			'l10n'              => array(
				'addNewVenue'  => $post_type_object->labels->add_new_item,
				'addVenue'     => esc_html__( 'Add a Venue', 'bandstand' ),
				'edit'         => esc_html__( 'Edit', 'bandstand' ),
				'manageVenues' => esc_html__( 'Select Venue', 'bandstand' ),
				'select'       => esc_html__( 'Select', 'bandstand' ),
				'selectVenue'  => esc_html__( 'Select Venue', 'bandstand' ),
				'venues'       => $post_type_object->labels->name,
				'view'         => esc_html__( 'View', 'bandstand' ),
			),
		);

		wp_localize_script( 'bandstand-gig-edit', '_bandstandVenueManagerSettings', $settings );
	}

	/**
	 * Exclude metadata from exports.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $result   Whether the metadata should be excluded.
	 * @param  string $meta_key Meta key.
	 * @return bool
	 */
	public function exclude_meta_from_export( $result, $meta_key ) {
		return $result;
	}

	/**
	 * Remap gig venues after an import.
	 *
	 * @todo Try to do this only when a gig or venue is imported.
	 * @todo May need to make this more efficient.
	 *
	 * @since 1.0.0
	 */
	public function remap_gig_venues() {
		global $wpdb;

		$results = $wpdb->get_results(
			"SELECT pm.post_id AS gig_id, p.ID as venue_id
			FROM $wpdb->posts p
			INNER JOIN $wpdb->postmeta pm ON pm.meta_key = 'bandstand_venue_guid' AND pm.meta_value = p.guid
			WHERE post_type = 'bandstand_venue'"
		);

		foreach ( $results as $result ) {
			get_bandstand_gig( $result->gig_id )->set_venue( $result->venue_id );
		}
	}

	/**
	 * Retrieve the base slug to use for past gigs rewrite rules.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_past_rewrite_base() {
		$slug = preg_replace( '/[^a-z0-9-_]/', '', _x( 'past', 'past gigs permalink slug', 'bandstand' ) );

		if ( empty( $slug ) ) {
			$slug = 'past';
		}

		return apply_filters( 'bandstand_past_gigs_rewrite_base', $slug );
	}

	/**
	 * Print a JSON-LD script tag for a list of posts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $posts Array of posts.
	 */
	protected function print_jsonld( $posts ) {
		$items = array();

		foreach ( $posts as $post ) {
			$items[] = $this->prepare_gig_for_jsonld( $post );
		}

		printf(
			'<script type="application/ld+json">%s</script>',
			wp_json_encode( $items )
		);
	}

	/**
	 * Format a gig for JSON-LD.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post $post Gig post object.
	 * @return array
	 */
	protected function prepare_gig_for_jsonld( $post ) {
		$gig = get_bandstand_gig( $post );

		$item = array(
			'@context'    => 'http://schema.org',
			'@type'       => 'MusicEvent',
			'name'        => esc_html( get_bandstand_gig_title( $post ) ),
			'startDate'   => esc_html( $gig->format_start_date( 'c' ) ),
			'description' => esc_html( get_bandstand_gig_description( $post ) ),
			'url'         => esc_url( get_permalink( $post ) ),
		);

		if ( has_post_thumbnail() ) {
			$item['image'] = esc_url( get_the_post_thumbnail_url( $post, 'full' ) );
		}

		/*$item['performer'] = array(
			'@type'  => '', // Organization, Person
			'name'   => '',
			//'image'  => '',
			//'sameAs' => '', // Wikipedia URL
			//'url'    => '',
		);*/

		if ( $gig->has_venue() ) {
			$venue = $gig->get_venue();

			$item['location'] = array(
				'@type'     => 'Place',
				'name'      => esc_html( $venue->get_name() ),
				'telephone' => esc_html( $venue->get_phone() ),
				'sameAs'    => esc_url( $venue->get_website_url() ),
				'address'   => array(
					'@type' => 'PostalAddress',
					'addressLocality' => esc_html( $venue->get_city() ),
					'addressRegion'   => esc_html( $venue->get_region() ),
					'postalCode'      => esc_html( $venue->get_postal_code() ),
					'streetAddress'   => esc_html( $venue->get_address() ),
					'addressCountry'  => esc_html( $venue->get_country() ),
				),
			);
		}

		$tickets_url = get_bandstand_gig_tickets_url( $post );
		if ( ! empty( $tickets_url ) ) {
			$item['offers'] = array(
				'@type' => 'Offer',
				'url'   => esc_url( $tickets_url ),
			);
		}

		return apply_filters( 'bandstand_prepare_gig_for_jsonld', $item, $post );
	}
}

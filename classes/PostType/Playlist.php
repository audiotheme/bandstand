<?php
/**
 * Cue playlist post type and integration.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for integration with the Cue playlist post type.
 *
 * @package Bandstand\Discography
 * @since   1.0.0
 */
class Bandstand_PostType_Playlist {
	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Bandstand_Plugin
	 */
	protected $plugin;

	/**
	 * Post type name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'cue_playlist';

	/**
	 * Set a reference to a plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @param Bandstand_Plugin $plugin Main plugin instance.
	 * @return $this
	 */
	public function set_plugin( Bandstand_Plugin $plugin ) {
		$this->plugin = $plugin;
		return $this;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 15 );
		add_action( 'print_media_templates', array( $this, 'print_templates' ) );
	}

	/**
	 * Enqueue playlist scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		if ( 'cue_playlist' !== get_current_screen()->id ) {
			return;
		}

		wp_enqueue_style( 'bandstand-playlist-admin', $this->plugin->get_url( 'admin/css/playlist.css' ) );

		wp_enqueue_script(
			'bandstand-playlist-admin',
			$this->plugin->get_url( 'admin/js/playlist.js' ),
			array( 'cue-admin' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'bandstand-playlist-admin', '_bandstandPlaylistSettings', array(
			'l10n' => array(
				'frameTitle'        => esc_html__( 'Bandstand Tracks', 'bandstand' ),
				'frameMenuItemText' => esc_html__( 'Add from Bandstand', 'bandstand' ),
				'frameButtonText'   => esc_html__( 'Add Tracks', 'bandstand' ),
			),
		) );
	}

	/**
	 * Print playlist JavaScript templates.
	 *
	 * @since 1.0.0
	 */
	public function print_templates() {
		?>
		<script type="text/html" id="tmpl-bandstand-playlist-record">
			<div class="bandstand-playlist-record-header">
				<img src="{{ data.thumbnail }}">
				<h4 class="bandstand-playlist-record-title"><em>{{ data.title }}</em> {{ data.artist }}</h4>
			</div>

			<ol class="bandstand-playlist-record-tracks">
				<# _.each( data.tracks, function( track ) { #>
					<li class="bandstand-playlist-record-track" data-id="{{ track.id }}">
						<span class="bandstand-playlist-record-track-cell">
							{{{ track.title }}}
						</span>
					</li>
				<# }); #>
			</ol>
		</script>
		<?php
	}
}

<?php
/**
 * Environment ompatibility checks and notices.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Class for checking environment compatibility.
 *
 * @package Bandstand
 * @since   1.0.0
 */
class Bandstand_Compatibility {
	/**
	 * Minimum PHP version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const MINIMUM_PHP_VERSION = '5.4';

	/**
	 * Display a notice about the minimum PHP version supported.
	 *
	 * @since 1.0.0
	 */
	public static function display_php_version_notice() {
		$notice = sprintf(
			wp_kses(
				__( 'Bandstand requires PHP %s or later to run. Your current version is %s.', 'bandstand' ),
				array( 'a' => array( 'href' => true ) )
			),
			self::MINIMUM_PHP_VERSION,
			esc_html( phpversion() )
		);

		self::display_notice( $notice );
	}

	/**
	 * Display an admin notice.
	 *
	 * @since 1.0.0
	 */
	protected static function display_notice( $message, $type = 'error' ) {
		?>
		<div class="bandstand-compatibility-notice notice notice-<?php echo esc_attr( $type ); ?>">
			<p>
				<?php echo $message; ?>
			</p>
		</div>
		<?php
	}
}

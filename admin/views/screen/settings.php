<?php
/**
 * View to display the settings screen.
 *
 * @package   Bandstand\Settings
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Bandstand Settings', 'bandstand' ); ?></h1>

	<form action="options.php" method="post">
		<?php settings_fields( 'bandstand-settings' ); ?>
		<?php do_settings_sections( 'bandstand-settings' ); ?>
		<?php submit_button(); ?>
	</form>
</div>

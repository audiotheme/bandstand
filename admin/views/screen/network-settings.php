<?php
/**
 * View to display the network settings screen.
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

	<form action="edit.php?action=bandstand-save-network-settings" method="post">
		<?php settings_fields( 'bandstand-network-settings' ); ?>
		<?php do_settings_sections( 'bandstand-network-settings' ); ?>
		<?php submit_button(); ?>
	</form>
</div>

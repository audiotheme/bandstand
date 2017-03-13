<?php
/**
 * View to display a dashboard screen header.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div class="wrap bandstand-dashboard-wrap">

<header class="bandstand-dashboard-header">
	<h1><?php esc_html_e( 'Welcome to Bandstand', 'bandstand' ); ?></h1>
	<p>
		<?php esc_html_e( 'Bandstand has the tools you need to easily manage your gigs, discography, videos and more.', 'bandstand' ); ?>
	</p>

	<div class="bandstand-badge">
		<?php include( $this->plugin->get_path( 'admin/images/dashicons/bandstand.svg' ) ); ?>
	</div>
</header>

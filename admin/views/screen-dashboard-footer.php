<?php
/**
 * View to display a dashboard screen footer.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div class="bandstand-dashboard-addendum">
	<aside>
		<h1><?php esc_html_e( 'Need Help?', 'bandstand' ); ?></h1>
		<p>
			<?php
			printf(
				wp_kses(
					__( 'Browse our <a href="%1$s" target="_blank">Frequently Asked Questions</a> or read the <a href="%2$s" target="_blank">Bandstand documentation</a>.', 'bandstand' ),
					array( 'a' => array( 'href' => true, 'target' => true ) )
				),
				'https://audiotheme.com/faqs/',
				'https://audiotheme.com/support/bandstand/'
			);
			?>

		</p>
		<p>
			<?php
			printf(
				wp_kses(
					__( 'For additional help, visit the AudioTheme website for <a href="%s" target="_blank">priority support</a>.', 'bandstand' ),
					array( 'a' => array( 'href' => true, 'target' => true ) )
				),
				'https://audiotheme.com/support/'
			);
			?>
		</p>
	</aside>

	<aside>
		<h1><?php esc_html_e( 'Email Updates', 'bandstand' ); ?></h1>
		<p>
			<?php esc_html_e( 'Sign up for the latest updates, discounts, new products and more.', 'bandstand' ); ?>
		</p>
		<form action="https://audiotheme.us2.list-manage.com/subscribe/post?u=09290a3b20d0fa9f786ecf6a0&amp;id=1e2ba34b92" method="post" target="_blank" novalidate>
			<label for="mce-EMAIL" class="screen-reader-text"><?php esc_html_e( 'Email Address', 'bandstand' ); ?></label>
			<input type="email" id="mce-EMAIL" name="EMAIL" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>">
			<input type="hidden" name="SOURCE" id="mce-SOURCE" value="Bandstand">
			<input type="submit" name="subscribe" id="mc-embedded-subscribe" value="Subscribe" class="button button-primary">
		</form>
	</aside>
</div>

<footer class="bandstand-dashboard-footer">
	<p>
		<a href="https://audiotheme.com/" target="_blank" class="bandstand-footer-logo"><svg viewBox="0 0 80 40">
			<path fill="#E4002B" d="M10,40C4.5,40,0,35.5,0,30s4.5-10,10-10s10,4.5,10,10S15.5,40,10,40z M0,0l40,40V20L20,0H0z M80,0H40v14h40V0z M74,40L60,18L46,40H74z"/>
		</svg></a>
	</p>
	<p>
		Bandstand <?php echo esc_html( BANDSTAND_VERSION ); ?> |
		<a href="https://twitter.com/AudioTheme"><?php esc_html_e( 'Follow @AudioTheme on Twitter', 'bandstand' ); ?></a>
	</p>
</footer>


<br class="clear">

</div>

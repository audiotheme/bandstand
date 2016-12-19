<?php
/**
 * The template used for displaying meta links on single record pages.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$record = get_bandstand_record();
?>

<?php if ( $record->has_links() ) : ?>

	<div class="bandstand-record-links">
		<h2 class="bandstand-record-links-title"><?php esc_html_e( 'Purchase', 'bandstand' ); ?></h2>
		<ul class="bandstand-record-links-list">
			<?php
			foreach ( $record->get_links() as $link ) {
				printf(
					'<li class="bandstand-record-links-item"><a href="%s" class="bandstand-record-link"%s itemprop="url">%s</a></li>',
					esc_url( $link['url'] ),
					( false === strpos( $link['url'], home_url() ) ) ? ' target="_blank"' : '',
					esc_html( $link['name'] )
				);
			}
			?>
		</ul>
	</div><!-- /.record-links -->

<?php endif; ?>

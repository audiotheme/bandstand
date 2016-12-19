<?php
/**
 * The template used for displaying metadata on single record pages.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$record = get_bandstand_record();
?>

<?php if ( $record->has_release_date() || $record->has_genre() || $record->has_label() ) : ?>
	<ul class="bandstand-record-meta bandstand-meta-list">
		<?php if ( $record->has_release_date() ) : ?>
			<li class="bandstand-meta-item">
				<span class="bandstand-label"><?php esc_html_e( 'Release', 'bandstand' ); ?></span>
				<span itemprop="dateCreated"><?php echo esc_html( $record->get_release_date() ); ?></span>
			</li>
		<?php endif; ?>

		<?php if ( $record->has_genre() ) : ?>
			<li class="bandstand-meta-item">
				<span class="bandstand-label"><?php esc_html_e( 'Genre', 'bandstand' ); ?></span>
				<?php
				the_bandstand_record_genres(
					'<span itemprop="genre">',
					'<span itemprop="genre">, </span>',
					'</span>'
				);
				?>
			</li>
		<?php endif; ?>

		<?php if ( $record->has_label() ) : ?>
			<li class="bandstand-meta-item">
				<span class="bandstand-label"><?php esc_html_e( 'Label', 'bandstand' ); ?></span>
				<span><?php echo esc_html( $record->get_label() ); ?></span>
			</li>
		<?php endif; ?>
	</ul><!-- /.record-meta -->
<?php endif; ?>

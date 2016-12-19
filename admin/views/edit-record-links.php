<?php
/**
 * View for the link repeater in the record details meta box.
 *
 * @package   Bandstand\Discography
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<table class="bandstand-repeater" id="record-links">
	<thead>
		<tr>
			<th colspan="3"><?php esc_html_e( 'Links', 'bandstand' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="2">
				<a class="button bandstand-repeater-add-item"><?php esc_html_e( 'Add URL', 'bandstand' ) ?></a>
				<?php
				printf(
					'<span class="bandstand-repeater-sort-warning" style="display: none;">%1$s <br /><em>%2$s</em></span>',
					esc_html__( 'The order has been changed.', 'bandstand' ),
					esc_html__( 'Save your changes.', 'bandstand' )
				);
				?>
			</td>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
	<tbody class="bandstand-repeater-items">
		<?php
		foreach ( $record_links as $i => $link ) :
			$link = wp_parse_args( $link, array( 'name' => '', 'url' => '' ) );
			?>
			<tr class="bandstand-repeater-item">
				<td style="padding-right: 5px; width: 35%"><input type="text" name="bandstand_record[record_links][<?php echo esc_attr( $i ); ?>][name]" value="<?php echo esc_attr( $link['name'] ); ?>" placeholder="<?php esc_attr_e( 'Text', 'bandstand' ); ?>" class="record-link-name regular-text bandstand-clear-on-add"></td>
				<td><input type="text" name="bandstand_record[record_links][<?php echo esc_attr( $i ); ?>][url]" value="<?php echo esc_url( $link['url'] ); ?>" placeholder="<?php esc_attr_e( 'URL', 'bandstand' ); ?>" class="widefat bandstand-clear-on-add"></td>
				<td class="column-action"><a class="bandstand-repeater-remove-item"><span class="dashicons dashicons-trash"></span></a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

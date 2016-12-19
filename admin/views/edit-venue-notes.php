<?php
/**
 * View to display the venue contact meta box.
 *
 * @package   Bandstand\Gigs
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<p>
	<?php esc_html_e( "Notes and contact information below are for personal use and won't be displayed on the front end.", 'bandstand' ); ?>
</p>

<table class="form-table">
	<tr>
		<th><label for="venue-contact-name"><?php esc_html_e( 'Name', 'bandstand' ) ?></label></th>
		<td><input type="text" name="bandstand_venue[contact_name]" id="venue-contact-name" class="regular-text" value="<?php echo esc_attr( $venue->contact_name ); ?>"></td>
	</tr>
	<tr>
		<th><label for="venue-contact-phone"><?php esc_html_e( 'Phone', 'bandstand' ) ?></label></th>
		<td><input type="text" name="bandstand_venue[contact_phone]" id="venue-contact-phone" class="regular-text" value="<?php echo esc_attr( $venue->contact_phone ); ?>"></td>
	</tr>
	<tr>
		<th><label for="venue-contact-email"><?php esc_html_e( 'Email', 'bandstand' ) ?></label></th>
		<td><input type="text" name="bandstand_venue[contact_email]" id="venue-contact-email" class="regular-text" value="<?php echo esc_attr( $venue->contact_email ); ?>"></td>
	</tr>
	<tr>
		<th><label for=""><?php esc_html_e( 'Notes', 'bandstand' ); ?></label></th>
		<td>
			<?php
			wp_editor( $notes, 'venuenotes', array(
				'editor_css'    => '<style type="text/css" scoped="true">.mceIframeContainer { background-color: #fff;}</style>',
				'media_buttons' => false,
				'textarea_name' => 'bandstand_venue[notes]',
				'textarea_rows' => 6,
				'teeny'         => true,
			) );
			?>
		</td>
	</tr>
</table>

<?php
/**
 * View to display a taxonomy meta box.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div id="taxonomy_<?php echo esc_attr( $taxonomy ); ?>" class="bandstand-taxonomy-meta-box" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">
	<div class="bandstand-taxonomy-term-list">
		<ul>
			<?php
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $id => $name ) {
					printf(
						'<li><label><input type="checkbox" name="bandstand_post_terms[%s][]" value="%d"%s> %s</label></li>',
						esc_attr( $taxonomy ),
						absint( $id ),
						checked( in_array( $id, $selected_ids, true ), true, false ),
						esc_html( $name )
					);
				}
			}
			?>
		</ul>
		<input type="hidden" name="bandstand_post_terms[<?php echo esc_attr( $taxonomy ); ?>][]" value="0">
	</div>

	<div class="bandstand-add-term-group hide-if-no-js">
		<label for="add-<?php echo esc_attr( $taxonomy ); ?>" class="screen-reader-text"><?php echo esc_html( $taxonomy_object->labels->add_new_item ); ?></label>
		<span class="bandstand-input-group">
			<input type="text" id="add-<?php echo esc_attr( $taxonomy ); ?>" class="bandstand-add-term-field bandstand-input-group-field">
			<span class="bandstand-input-group-button">
				<input type="button" value="<?php echo esc_attr( $button_text ); ?>" class="button button-secondary bandstand-button-load">
			</span>
		</span>
		<input type="hidden" class="bandstand-add-term-nonce" value="<?php echo esc_attr( wp_create_nonce( 'add-term_' . $taxonomy ) ); ?>">
		<span class="bandstand-add-term-response"></span>
	</div>
</div>

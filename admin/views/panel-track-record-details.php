<div class="bandstand-track-record-panel bandstand-panel">
	<div class="bandstand-panel-header">
		<h2 class="bandstand-panel-title"><?php echo esc_html( get_the_title( $record->ID ) ); ?></h2>
	</div>

	<div class="bandstand-panel-body">

		<div class="bandstand-record-card">
			<?php
			if ( has_post_thumbnail( $record->ID ) ) {
				printf(
					'<div class="bandstand-record-card-thumbnail">%s</div>',
					get_the_post_thumbnail( $record->ID, 'thumbnail' )
				);
			}
			?>

			<div class="bandstand-record-card-details">

				<table>
					<?php if ( $record->has_artist() ) : ?>
						<tr>
							<th><?php esc_html_e( 'Artist:', 'bandstand' ); ?></th>
							<td><?php echo esc_html( $record->get_artist() ); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ( $record->has_release_date() ) : ?>
						<tr>
							<th><?php esc_html_e( 'Release:', 'bandstand' ); ?></th>
							<td><?php echo esc_html( $record->get_release_date() ); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ( $record->has_label() ) : ?>
						<tr>
							<th><?php esc_html_e( 'Label:', 'bandstand' ); ?></th>
							<td><?php echo esc_html( $record->get_label() ); ?></td>
						</tr>
					<?php endif; ?>

					<?php if ( $record->has_catalog_number() ) : ?>
						<tr>
							<th><?php esc_html_e( 'Catalog No.:', 'bandstand' ); ?></th>
							<td><?php echo esc_html( $record->get_catalog_number() ); ?></td>
						</tr>
					<?php endif; ?>
				</table>

				<p>
					<a href="<?php echo esc_url( get_edit_post_link( $record->ID ) ); ?>" class="button"><?php echo esc_html( $record_post_type_object->labels->edit_item ); ?></a>
				</p>

			</div>
		</div>

	</div>
</div>

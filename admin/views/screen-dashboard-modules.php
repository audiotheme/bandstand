<?php
/**
 * View to modules on the dashboard screen.
 *
 * @package   Bandstand\Administration
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

?>

<div class="bandstand-dashboard-lead">
	<p>
		<?php esc_html_e( 'Gigs, Discography, and Videos are the backbone of Bandstand. Explore each feature below or use the menu options to the left to get started.', 'bandstand' ); ?>
	</p>
</div>

<div class="bandstand-module-cards">

	<?php foreach ( $modules as $module ) :
		$classes   = array( 'bandstand-module-card', 'bandstand-module-card--' . $module->id );
		$classes[] = $module->is_active() ? 'is-active' : 'is-inactive';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-module-id="<?php echo esc_attr( $module->id ); ?>">

			<div class="bandstand-module-card-details">
				<h2 class="bandstand-module-card-name"><?php echo esc_html( $module->name ); ?></h2>
				<div class="bandstand-module-card-description">
					<?php echo wpautop( esc_html( $module->description ) ); ?>
				</div>
				<div class="bandstand-module-card-overview">
					<?php if ( method_exists( $module, 'display_overview' ) ) : ?>
						<?php $module->display_overview(); ?>
					<?php else : ?>
						<?php do_action( 'bandstand_module_card_overview', $module->id ); ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="bandstand-module-card-actions">
				<div class="bandstand-module-card-actions-primary">
					<?php if ( method_exists( $module, 'display_primary_button' ) ) : ?>
						<?php $module->display_primary_button(); ?>
					<?php else : ?>
						<?php do_action( 'bandstand_module_card_primary_button', $module->id ); ?>
					<?php endif; ?>
				</div>

				<div class="bandstand-module-card-actions-secondary">
					<a href=""><?php esc_html_e( 'Details', 'bandstand' ); ?></a>
				</div>
			</div>

		</div>
	<?php endforeach; ?>

</div>

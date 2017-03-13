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
		<?php esc_html_e( 'Add a new gig, record, or video to get started.', 'bandstand' ); ?>
	</p>
</div>

<div class="bandstand-module-cards">

	<?php foreach ( $modules as $module ) :
		$classes   = array( 'bandstand-module-card', 'bandstand-module-card--' . $module->id );
		$classes[] = $module->is_active() ? 'is-active' : 'is-inactive';
		?>
		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-module-id="<?php echo esc_attr( $module->id ); ?>">
			<div class="bandstand-module-card-header">
				<h2 class="bandstand-module-card-name"><?php echo esc_html( $module->name ); ?></h2>
			</div>

			<div class="bandstand-module-card-body">
				<div class="bandstand-module-card-description">
					<?php echo wpautop( esc_html( $module->description ) ); ?>
				</div>

				<?php if ( method_exists( $module, 'display_primary_button' ) ) : ?>
					<?php $module->display_primary_button(); ?>
				<?php else : ?>
					<?php do_action( 'bandstand_module_card_primary_button', $module->id ); ?>
				<?php endif; ?>
			</div>

		</div>
	<?php endforeach; ?>

</div>

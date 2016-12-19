<?php
/**
 * The template to display a single gig.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

$gig   = get_bandstand_gig();
$venue = $gig->get_venue();
?>

<dl id="bandstand-gig" <?php post_class( array( 'bandstand-gig-single', 'bandstand-clearfix' ) ) ?> itemscope itemtype="http://schema.org/MusicEvent">

	<?php if ( $gig->has_venue() ) : ?>

		<dt class="bandstand-gig-header">
			<?php the_title( '<h1 class="bandstand-gig-title entry-title" itemprop="name">', '</h1>' ); ?>

			<div class="bandstand-gig-date">
				<meta content="<?php echo esc_attr( $gig->format_start_date( 'c' ) ); ?>" itemprop="startDate">
				<time datetime="<?php echo esc_attr( $gig->format_start_date( 'c' ) ); ?>">
					<strong><?php echo esc_html( $gig->format_start_date( 'F d, Y' ) ); ?></strong>
				</time>
			</div><!-- /.gig-date -->
		</dt><!-- /.gig-header -->

	<?php endif; ?>

	<dd class="bandstand-gig-description">
		<?php if ( $gig->has_venue() ) : ?>

			<p class="bandstand-gig-place">
				<?php echo get_bandstand_venue_location( $venue->ID ); ?>
			</p>

		<?php endif; ?>

		<?php the_bandstand_gig_description( '<div class="bandstand-gig-note" itemprop="description">', '</div>' ); ?>
	</dd><!-- /.gig-description -->

	<dd class="bandstand-gig-meta bandstand-meta-list">
		<span class="bandstand-gig-time bandstand-meta-item">
			<strong class="bandstand-label"><?php esc_html_e( 'Time', 'bandstand' ); ?></strong>
			<?php echo $gig->format_start_date( '', 'g:i A', array( 'utc' => true, 'empty_time' => esc_html__( 'TBD', 'bandstand' ) ) ); ?>
		</span>

		<?php if ( bandstand_gig_has_ticket_meta() ) : ?>

			<span class="bandstand-gig-tickets bandstand-meta-item" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<strong class="bandstand-label"><?php esc_html_e( 'Admission', 'bandstand' ); ?></strong>

				<?php if ( $gig_tickets_price = get_bandstand_gig_tickets_price() ) : ?>
					<span class="bandstand-gig-tickets-price" itemprop="price"><?php echo esc_html( $gig_tickets_price ); ?></span>
				<?php endif; ?>

				<?php if ( $gig_tickets_url = get_bandstand_gig_tickets_url() ) : ?>
					<span class="bandstand-gig-tickets-link"><a href="<?php echo esc_url( $gig_tickets_url ); ?>" target="_blank" itemprop="url"><?php esc_html_e( 'Buy Tickets', 'bandstand' ); ?></a></span>
				<?php endif; ?>
			</span>

		<?php endif; ?>

	</dd><!-- /.gig-meta -->

	<?php if ( $gig->has_venue() ) : ?>

		<dd class="bandstand-gig-venue bandstand-clearfix" itemprop="location" itemscope itemtype="http://schema.org/EventVenue">
			<?php
			the_bandstand_venue_vcard( array(
				'container'         => '',
				'show_name_link'    => false,
				'show_phone'        => false,
				'separator_country' => ', ',
			) );
			?>

			<div class="bandstand-venue-meta">
				<?php if ( $venue->has_phone() ) : ?>
					<span class="bandstand-venue-phone"><?php echo esc_html( $venue->get_phone() ); ?></span>
				<?php endif; ?>

				<?php if ( $venue->has_website_url() ) : ?>
					<span class="bandstand-venue-website"><a href="<?php echo esc_url( $venue->get_website_url() ); ?>" itemprop="url"><?php echo bandstand_simplify_url( $venue->get_website_url() ); ?></a></span>
				<?php endif; ?>
			</div>

			<div class="bandstand-venue-map">
				<?php echo get_bandstand_google_map_embed( array( 'width' => '100%', 'height' => 220 ), $venue->ID ); ?>
			</div>
		</dd><!-- /.gig-venue -->

	<?php endif; ?>

	<dd class="bandstand-content entry-content">

		<?php the_content(); ?>

	</dd><!-- /.gig-content -->

</dl><!-- /#bandstand-gig -->

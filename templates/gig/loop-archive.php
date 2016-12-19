<?php
/**
 * The template to display a gig archive loop.
 *
 * @package   Bandstand\Template
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

do_action( 'bandstand_before_loop' );
?>

<ul id="bandstand-gigs" <?php bandstand_posts_container_class( 'bandstand-gigs bandstand-clearfix no-fouc' ); ?> data-bandstand-media-classes="400,600">

	<?php
	while ( have_posts() ) :
		the_post();
		$gig = get_bandstand_gig();
		?>

		<li <?php post_class( array( 'bandstand-gig-card', 'bandstand-clearfix' ) ) ?> itemscope itemtype="http://schema.org/MusicEvent">

			<div class="bandstand-gig-meta-datetime">
				<meta content="<?php echo $gig->format_start_date( 'c' ); ?>" itemprop="startDate">
				<time datetime="<?php echo $gig->format_start_date( 'c' ); ?>">
					<span class="bandstand-gig-date"><a href="<?php the_permalink(); ?>" class="bandstand-gig-permalink" itemprop="url"><?php echo $gig->format_start_date( get_option( 'date_format' ) ); ?></a></span>
					<span class="bandstand-gig-time"><?php echo $gig->format_start_date( '', get_option( 'time_format' ) ); ?></span>
				</time>
			</div><!-- /.gig-meta-datetime -->

			<div class="bandstand-gig-details">

				<?php the_title( '<h2 class="bandstand-gig-title" itemprop="name">', '</h2>' ); ?>

				<?php if ( $gig->has_venue() ) : ?>

					<p class="bandstand-gig-place" itemprop="location" itemscope itemtype="http://schema.org/EventVenue">

						<span class="bandstand-gig-location"><?php echo get_bandstand_venue_location( $gig->get_venue()->ID ); ?></span>

						<?php
						the_bandstand_gig_venue_link( array(
							'before'      => '<span class="bandstand-gig-venue">',
							'after'       => '</span>',
							'before_link' => '<span itemprop="name">',
							'after_link'  => '</span>',
						) );
						?>
					</p>

				<?php endif; ?>

				<?php the_bandstand_gig_description( '<div class="bandstand-gig-note" itemprop="description">', '</div>' ); ?>

			</div><!-- /.gig-details -->

			<?php if ( bandstand_gig_has_ticket_meta() ) : ?>

				<div class="bandstand-gig-meta-tickets" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

					<?php if ( $gig_tickets_price = get_bandstand_gig_tickets_price() ) : ?>
						<span class="bandstand-gig-tickets-price" itemprop="price"><?php echo esc_html( $gig_tickets_price ); ?></span>
					<?php endif; ?>

					<?php if ( $gig_tickets_url = get_bandstand_gig_tickets_url() ) : ?>
						<span class="bandstand-gig-tickets-link" >
							<a href="<?php echo esc_url( $gig_tickets_url ); ?>" target="_blank" itemprop="url"><?php esc_html_e( 'Buy Tickets', 'bandstand' ); ?></a>
						</span>
					<?php endif; ?>

				</div><!-- /.gig-meta-tickets -->

			<?php endif; ?>

		</li><!-- /.bandstand-gig-card -->

	<?php endwhile; ?>

</ul><!-- /#bandstand-gigs -->

<?php do_action( 'bandstand_after_loop' ); ?>

<?php
the_posts_navigation( array(
	'prev_text' => esc_html__( 'Previous', 'bandstand' ),
	'next_text' => esc_html__( 'Next', 'bandstand' ),
) );

<?php
/**
 * Template to display a Gigs widget.
 *
 * @package Bandstand\Template
 * @since 1.0.0
 */

?>

<?php
if ( ! empty( $title ) ) :
	echo $before_title . $title . $after_title;
endif;
?>

<?php if ( $loop->have_posts() ) : ?>

	<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		<?php $gig = get_bandstand_gig(); ?>

		<dl class="gig-card vevent" itemscope itemtype="http://schema.org/MusicEvent">

			<?php the_title( '<dt class="gig-card-title"><a href="' . esc_url( get_permalink() ) . '" class="url uid" itemprop="url"><span class="summary" itemprop="name">', '</span></a></dt>' ); ?>

			<dd class="gig-card-date date">
				<meta content="<?php echo esc_attr( $gig->format_start_date( 'c' ) ); ?>" itemprop="startDate">

				<time class="dtstart" datetime="<?php echo esc_attr( $gig->format_start_date( 'c' ) ); ?>">
					<?php echo $gig->format_start_date( $date_format ); ?>
				</time>
			</dd>

			<?php if ( $gig->has_venue() ) : ?>
				<dd class="gig-card-venue"><?php echo esc_html( $gig->get_venue()->get_name() ); ?></dd>

				<dd class="gig-card-location location">
					<?php echo get_bandstand_gig_location(); ?>
				</dd>
			<?php endif; ?>

			<?php the_bandstand_gig_description( '<dd class="gig-card-description">', '</dd>' ); ?>

		</dl>

	<?php endwhile; ?>

	<?php if ( ! empty( $link_text ) ) : ?>
		<p class="more">
			<a href="<?php echo esc_url( esc_url( get_post_type_archive_link( 'bandstand_gig' ) ) ); ?>"><?php echo esc_html( $link_text ); ?></a>
		</p>
	<?php endif; ?>

<?php endif; ?>

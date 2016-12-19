<?php
/**
 * Template to display a Record widget.
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

<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
	<p class="featured-image">
		<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo get_the_post_thumbnail( $post->ID, $image_size ); ?></a>
	</p>
<?php endif; ?>

<?php
if ( ! empty( $text ) ) :
	echo '<div class="widget-description">' . wpautop( $text ) . '</div>';
endif;
?>

<?php if ( ! empty( $link_text ) ) : ?>
	<p class="more">
		<a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( $link_text ); ?></a>
	</p>
<?php endif; ?>

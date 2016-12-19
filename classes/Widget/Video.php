<?php
/**
 * Video widget.
 *
 * Display a selected video in a widget area.
 *
 * @package   Bandstand\Widgets
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Video widget class.
 *
 * @package Bandstand\Widgets
 * @since   1.0.0
 */
class Bandstand_Widget_Video extends WP_Widget {
	/**
	 * Set up widget options.
	 *
	 * @since 1.0.0
	 * @see WP_Widget::construct()
	 */
	public function __construct() {
		$widget_options = array(
			'classname'                   => 'widget_bandstand_video',
			'customize_selective_refresh' => true,
			'description'                 => esc_html__( 'Display a video', 'bandstand' ),
		);

		parent::__construct( 'bandstand-video', esc_html__( 'Video (Bandstand)', 'bandstand' ), $widget_options );
	}

	/**
	 * Default widget front end display method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Arguments specific to the widget area.
	 * @param array $instance Widget instance settings.
	 */
	public function widget( $args, $instance ) {
		if ( empty( $instance['post_id'] ) ) {
			return;
		}

		$instance['title_raw'] = empty( $instance['title'] ) ? '' : $instance['title'];
		$instance['title']     = apply_filters( 'widget_title', $instance['title_raw'], $instance, $this->id_base );
		$instance['title']     = apply_filters( 'bandstand_widget_title', $instance['title'], $instance, $args, $this->id_base );

		$image_size = apply_filters( 'bandstand_widget_video_image_size', 'thumbnail', $instance, $args );
		$image_size = apply_filters( 'bandstand_widget_video_image_size-' . $args['id'], $image_size, $instance, $args );

		$data                 = array();
		$data['after_title']  = $args['after_title'];
		$data['before_title'] = $args['before_title'];
		$data['image_size']   = $image_size;
		$data['post']         = get_post( $instance['post_id'] );
		$data                 = array_merge( $instance, $data );

		echo $args['before_widget'];

		$template_loader = bandstand()->templates->loader;
		$template = $template_loader->locate_template( array( "widgets/{$args['id']}_video.php", 'widgets/video.php' ) );
		$template_loader->load_template( $template, $data );

		echo $args['after_widget'];
	}

	/**
	 * Form to modify widget instance settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current widget instance settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'link_text' => '',
			'post_id'   => '',
			'text'      => '',
			'title'     => '',
		) );

		$videos = get_posts( array(
			'post_type'      => 'bandstand_video',
			'orderby'        => 'title',
			'order'          => 'asc',
			'posts_per_page' => -1,
		) );

		$title = wp_strip_all_tags( $instance['title'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bandstand' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" class="widefat">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>"><?php esc_html_e( 'Video:', 'bandstand' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" class="widefat">
				<option value=""></option>
				<?php
				foreach ( $videos as $video ) {
					printf(
						'<option value="%d"%s>%s</option>',
						absint( $video->ID ),
						selected( $instance['post_id'], $video->ID, false ),
						esc_html( $video->post_title )
					);
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Description:', 'bandstand' ); ?></label>
			<textarea name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" cols="20" rows="5" class="widefat"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>"><?php esc_html_e( 'Link Text:', 'bandstand' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'link_text' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>" value="<?php echo esc_attr( $instance['link_text'] ); ?>" class="widefat">
		</p>
		<?php
	}

	/**
	 * Save widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New widget settings.
	 * @param array $old_instance Old widget settings.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['title']     = wp_strip_all_tags( $new_instance['title'] );
		$instance['link_text'] = wp_strip_all_tags( $new_instance['link_text'] );
		$instance['post_id']   = absint( $new_instance['post_id'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return $instance;
	}
}

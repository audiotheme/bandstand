<?php
/**
 * Gigs list widget.
 *
 * Display a list of gigs in a widget area.
 *
 * @package   Bandstand\Widgets
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

/**
 * Gigs list widget class.
 *
 * @package Bandstand\Widgets
 * @since   1.0.0
 */
class Bandstand_Widget_Gigs extends WP_Widget {
	/**
	 * Set up widget options.
	 *
	 * @since 1.0.0
	 * @see WP_Widget::construct()
	 */
	public function __construct() {
		$widget_options = array(
			'classname'                   => 'widget_bandstand_gigs',
			'customize_selective_refresh' => true,
			'description'                 => esc_html__( 'Display a list of gigs', 'bandstand' ),
		);

		parent::__construct( 'bandstand-gigs', esc_html__( 'Gigs (Bandstand)', 'bandstand' ), $widget_options );
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
		$instance['title_raw'] = empty( $instance['title'] ) ? '' : $instance['title'];
		$instance['title']     = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$instance['title']     = apply_filters( 'bandstand_widget_title', $instance['title'], $instance, $args, $this->id_base );

		$instance['date_format'] = apply_filters( 'bandstand_widget_gigs_date_format', get_option( 'date_format' ) );
		$instance['number']      = empty( $instance['number'] ) || ! absint( $instance['number'] ) ? 5 : absint( $instance['number'] );
		$instance['range']       = $this->bandstand_sanitize_range( $instance['range'] );

		$loop_args = array(
			'no_found_rows'  => true,
			'posts_per_page' => $instance['number'],
		);

		// @todo Move these options into the gig query?
		if ( 'past' === $instance['range'] ) {
			$loop_args['order'] = 'desc';

			$loop_args['meta_query'][] = array(
				'key'     => 'bandstand_upcoming_until_utc',
				'value'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
				'compare' => '<',
				'type'    => 'DATE',
			);
		} elseif ( 'today' === $instance['range'] ) {
			$loop_args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'bandstand_upcoming_until_utc',
					'value'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
					'compare' => '=',
					'type'    => 'DATE',
				),
				array(
					'key'     => 'bandstand_start_date',
					'value'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
					'compare' => '=',
					'type'    => 'DATE',
				),
			);
		}

		$loop = new Bandstand_Query_Gigs( apply_filters( 'bandstand_widget_gigs_loop_args', $loop_args ) );

		$data                 = array();
		$data['after_title']  = $args['after_title'];
		$data['before_title'] = $args['before_title'];
		$data['loop']         = $loop;
		$data                 = array_merge( $instance, $data );

		// Add a class with the number of gigs to display.
		$output = preg_replace( '/class="([^"]+)"/', 'class="$1 widget-items-' . $instance['number'] . '"', $args['before_widget'] );

		ob_start();
		$template_loader = bandstand()->templates->loader;
		$template = $template_loader->locate_template( array( "widgets/{$args['id']}_gigs.php", 'widgets/gigs.php' ) );
		$template_loader->load_template( $template, $data );
		$output .= ob_get_clean();

		$output .= $args['after_widget'];
		echo $output;

		wp_reset_postdata();
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
			'range'     => 'upcoming',
			'title'     => '',
		) );

		$title  = wp_strip_all_tags( $instance['title'] );
		$number = isset( $instance['number'] ) ? $instance['number'] : 5;
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bandstand' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'range' ) ); ?>"><?php esc_html_e( 'Range:', 'bandstand' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'range' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'range' ) ); ?>" class="widefat">
				<option value="upcoming"<?php selected( $instance['range'], 'upcoming' ); ?>><?php esc_html_e( 'Display upcoming gigs', 'bandstand' ); ?></option>
				<option value="today"<?php selected( $instance['range'], 'today' ); ?>><?php esc_html_e( "Display today's gigs", 'bandstand' ); ?></option>
				<option value="past"<?php selected( $instance['range'], 'past' ); ?>><?php esc_html_e( 'Display past gigs', 'bandstand' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of gigs to show:', 'bandstand' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" value="<?php echo absint( $number ); ?>" size="3">
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
		$instance['number']    = absint( $new_instance['number'] );
		$instance['link_text'] = wp_strip_all_tags( $new_instance['link_text'] );
		$instance['range']     = $this->bandstand_sanitize_range( $new_instance['range'] );

		return $instance;
	}

	/**
	 * Sanitize the range setting.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $value Range identifier.
	 * @return string
	 */
	public function bandstand_sanitize_range( $value ) {
		$valid_ranges = array( 'past', 'today', 'upcoming' );

		if ( ! in_array( $value, $valid_ranges ) ) {
			$value = 'upcoming';
		}

		return $value;
	}
}

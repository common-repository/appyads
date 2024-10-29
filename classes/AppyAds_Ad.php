<?php
/**
 * Add function to widgets_init that'll load the AppyAds widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'appyads_loadWidget' );

/**
 * Register the AppyAds widget.
 * 'AppyAds_Ad' is the widget class used below.
 *
 * @since 0.1
 */
function appyads_loadWidget() {
	register_widget( 'AppyAds_Ad' );
}

/**
 * AppyAds_Ad class.
 * This class handles all widget necessities: settings, form, display, and update.
 *
 * @since 0.1
 */
class AppyAds_Ad extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'appyad', 'description' => __('Display an AppyAds ad in your webpage.', 'appyad') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 250, 'id_base' => 'appyads-ad' );

		/* Create the widget. */
		$this->WP_Widget( 'appyads-ad', __('AppyAds Ad', 'appyad'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		//$title = false; //apply_filters('widget_title', $instance['title'] ); // Not used
		//$name = false;  //$instance['name']; // Not used
		$size = $instance['size'];

		/* Before widget (defined by theme). */
		echo $before_widget;

		/* The widget title is not displayed for AppyAds. (before and after defined by theme). */
		//if ( $title ) echo $before_title . $title . $after_title;
        
        /* The widget name is not displayed for AppyAds. */
		//if ( $name )	printf( '<p>' . __('This AppyAds name is %1$s.', 'example') . '</p>', $name );

        /* Display the AppyAds div for displaying an ad */
		echo appyads_placementMarkup($size);
        
        /* Enque the AppyAds javascript */
        appyads_enqueScript();

		/* After widget (defined by theme). */
		echo $after_widget;
	}

	/**
	 * Update the AppyAds widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for input entries (just to be safe). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['size'] = strip_tags( $new_instance['size'] );

		return $instance;
	}

	/**
	 * Displays the AppyAds widget settings controls on the widget panel.
	 */
	function form( $instance ) {
		/* Set up default widget settings. */
		$defaults = array( 'title' => __('', 'example'), 'name' => __('AppyAd', 'example'), 'size' => APPYADS_DEFAULT_CAMPAIGN_SIZE );
		$instance = wp_parse_args( (array) $instance, $defaults );
        echo appyads_getAppyAdsLogo(); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e('AppyAds size:', 'example'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" class="widefat" style="width:100%;">
                <?php
                $campSizes = appyads_getCampaignSizes();
                foreach ($campSizes as $size)
                    echo "<option value=\"$size\"" . ($instance['size'] == $size ? ' selected="selected"' : '') . ">$size</option>";
                ?>
			</select>
		</p>
	<?php
	}
}

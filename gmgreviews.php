<?php
/*
Plugin Name: GMG Testimonials
Plugin URI: http://www.goebelmedia.com
Description:Goebel Media Reviews Plugin
Version: 1.0
Author: Goebel Media
Author URI: http://www.goebelmedia.com
Text Domain: goebelmedia

------------------------------------------------------------------------

*/


//Register Styles and Scripts for later inclusion
wp_register_script( 'bxslider',  plugin_dir_url( __FILE__ ) . 'includes/js/jquery.bxslider.min.js', array( 'jquery' ), '1.0.0' );
wp_register_script( 'powerhouseslider',  plugin_dir_url( __FILE__ ) . 'includes/js/powerhouseslider.js', array( 'jquery','bxslider' ), '1.0.0' );
wp_register_style( 'bxslider-css',  plugin_dir_url( __FILE__ ) . 'includes/css/jquery.bxslider.css', array(), '1.0.0' );
wp_register_style( 'font-awesome',  plugin_dir_url( __FILE__ ) . 'includes/css/font-awesome.min.css', array(), '1.0.0' );
wp_register_style( 'reviews', plugin_dir_url( __FILE__ ) . 'includes/css/phreviews.css',false,'1.0','all');

//Enqueue Mandatory Stylesheet
wp_enqueue_style( 'reviews');

function create_ph_ptype() {
    register_post_type( 'ph_testimonials_7839',
        array(
            'labels' => array(
                'name' => 'Testimonials',
                'singular_name' => 'Testimonial',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Testimonial',
                'edit' => 'Edit',
                'edit_item' => 'Edit Testimonial',
                'new_item' => 'New Testimonial',
                'view' => 'View',
                'view_item' => 'View Testimonial',
                'search_items' => 'Search Testimonial',
                'not_found' => 'No Testimonials found',
                'not_found_in_trash' => 'No Testimonials found in Trash',
                'parent' => 'Parent Testimonial'
            ),
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-star-half',
            'has_archive' => false,
			'exclude_from_search' => true,
			'hierarchical' => false,
        )
    );
}

class ph_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'ph_widget', 
		__('GMG Testimonials Slider Widget', 'ph_widget_domain'), 
		array( 'description' => __( 'Display GMG Testimonials Review Slider', 'ph_widget_domain' ), ) 
		);
	}
	public function widget( $args, $instance ) {
		wp_enqueue_script( 'bxslider');
		wp_enqueue_script( 'powerhouseslider');
		wp_enqueue_style( 'bxslider-css');
		wp_enqueue_style( 'font-awesome');
		$title = apply_filters( 'widget_title', $instance['title'] );
		$showstars = $instance['stars'];
		$contentshown = $instance['contentshown'];
		$randomize = $instance['randomize'];
		$maxshown = $instance['maxshown'];
		if ( empty( $maxshown ) ) {
			$maxshown = 5;
		}
		if ($randomize) {
			$randomize = 'rand';
		} else {
			$randomize = 'title';
		}
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		$args = array(
			'post_type' => 'ph_testimonials_7839',
			'posts_per_page'=> $maxshown,
			'orderby' => $randomize,
			'order' => 'ASC'
		);
		$loop = new WP_Query( $args );
		if( $loop->have_posts() ) {
			?><ul class="bxslider powerhouse-slider"><?php
			while( $loop->have_posts() ): $loop->the_post();
				?>
				<li>
					<?php if ($showstars){ ?>
						<?php// $rating = get_post_meta( get_the_ID(), 'rating_value', true); ?>
						<?php $rating = get_field('rating_value'); ?>
						<?php if ($rating) { ?>
							<div class="powerhouse-stars">
							<?php while($rating > 0) {?>
								<i class="fa fa-star" aria-hidden="true"></i>
								<?php $rating--; ?>
								<?php $rating--; ?>
							<?php } ?>
							</div>
						<?php } ?>
					<?php } ?>
					<?php if ($contentshown){ ?>
						<?php the_content(); ?>
					<?php } ?>
					<p><strong><?php echo get_the_title();?></strong></p>
				</li>
				<?php
			endwhile;
			?></ul><?php
		}
		echo $args['after_widget'];
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'ph_widget_domain' );
		}
		if ( isset( $instance[ 'maxshown' ] ) ) {
			$maxshown = $instance[ 'maxshown' ];
		}
		else {
			$maxshown = __( '5', 'ph_widget_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'stars' ); ?>"><?php _e( 'Show Testimonial Stars?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'stars' ); ?>" name="<?php echo $this->get_field_name( 'stars' ); ?>" type="checkbox" <?php checked( $instance[ 'stars' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'contentshown' ); ?>"><?php _e( 'Show Testimonial Contents?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'contentshown' ); ?>" name="<?php echo $this->get_field_name( 'contentshown' ); ?>" type="checkbox" <?php checked( $instance[ 'contentshown' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'randomize' ); ?>"><?php _e( 'Randomize Testimonials?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'randomize' ); ?>" name="<?php echo $this->get_field_name( 'randomize' ); ?>" type="checkbox" <?php checked( $instance[ 'randomize' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'maxshown' ); ?>"><?php _e( 'Limit Testimonials Shown:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'maxshown' ); ?>" name="<?php echo $this->get_field_name( 'maxshown' ); ?>" type="number" value="<?php echo esc_attr( $maxshown ); ?>" min="2" max="60"/>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['stars'] = ( ! empty( $new_instance['stars'] ) ) ? strip_tags( $new_instance['stars'] ) : '';
		$instance['contentshown'] = ( ! empty( $new_instance['contentshown'] ) ) ? strip_tags( $new_instance['contentshown'] ) : '';
		$instance['randomize'] = ( ! empty( $new_instance['randomize'] ) ) ? strip_tags( $new_instance['randomize'] ) : '';
		$instance['maxshown'] = ( ! empty( $new_instance['maxshown'] ) ) ? strip_tags( $new_instance['maxshown'] ) : '';
		return $instance;
	}
}

class ph_static_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		'ph_static_widget', 
		__('GMG Testimonials Widget', 'ph_widget_domain'), 
		array( 'description' => __( 'Display GMG Testimonials', 'ph_widget_domain' ), ) 
		);
	}
	public function widget( $args, $instance ) {
		wp_enqueue_style( 'font-awesome');
		$title = apply_filters( 'widget_title', $instance['title'] );
		$showstars = $instance['stars'];
		$contentshown = $instance['contentshown'];
		$randomize = $instance['randomize'];
		$maxshown = $instance['maxshown'];
		if ( empty( $maxshown ) ) {
			$maxshown = 5;
		}
		if ($randomize) {
			$randomize = 'rand';
		} else {
			$randomize = 'title';
		}
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		$args = array(
			'post_type' => 'ph_testimonials_7839',
			'posts_per_page'=> $maxshown,
			'orderby' => $randomize,
			'order' => 'ASC'
		);
		$loop = new WP_Query( $args );
		if( $loop->have_posts() ) {
			?><div class="powerhouse-static"><?php
			while( $loop->have_posts() ): $loop->the_post();
				?>
				<div class="powerhouse-review">
					<?php if ($showstars){ ?>
						<?php //$rating = get_post_meta( get_the_ID(), 'rating_value', true); ?>
						<?php $rating = get_field('rating_value'); ?>
						<?php if ($rating) { ?>
							<div class="powerhouse-stars">
							<?php while($rating > 0) {?>
								<i class="fa fa-star" aria-hidden="true"></i>
								<?php $rating--; ?>
								<?php $rating--; ?>
							<?php } ?>
							</div>
						<?php } ?>
					<?php } ?>
					<?php if ($contentshown){ ?>
						<?php the_content(); ?>
					<?php } ?>
					<p><strong><?php echo get_the_title();?></strong></p>
				</div>
				<?php
			endwhile;
			?></div><?php
		}
		echo $args['after_widget'];
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'ph_widget_domain' );
		}
		if ( isset( $instance[ 'maxshown' ] ) ) {
			$maxshown = $instance[ 'maxshown' ];
		}
		else {
			$maxshown = __( '5', 'ph_widget_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'stars' ); ?>"><?php _e( 'Show Testimonial Stars?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'stars' ); ?>" name="<?php echo $this->get_field_name( 'stars' ); ?>" type="checkbox" <?php checked( $instance[ 'stars' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'contentshown' ); ?>"><?php _e( 'Show Testimonial Contents?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'contentshown' ); ?>" name="<?php echo $this->get_field_name( 'contentshown' ); ?>" type="checkbox" <?php checked( $instance[ 'contentshown' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'randomize' ); ?>"><?php _e( 'Randomize Testimonials?' ); ?></label> 
		<input class="checkbox" id="<?php echo $this->get_field_id( 'randomize' ); ?>" name="<?php echo $this->get_field_name( 'randomize' ); ?>" type="checkbox" <?php checked( $instance[ 'randomize' ], 'on' ); ?> />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'maxshown' ); ?>"><?php _e( 'Limit Testimonials Shown:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'maxshown' ); ?>" name="<?php echo $this->get_field_name( 'maxshown' ); ?>" type="number" value="<?php echo esc_attr( $maxshown ); ?>" min="2" max="60"/>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['stars'] = ( ! empty( $new_instance['stars'] ) ) ? strip_tags( $new_instance['stars'] ) : '';
		$instance['contentshown'] = ( ! empty( $new_instance['contentshown'] ) ) ? strip_tags( $new_instance['contentshown'] ) : '';
		$instance['randomize'] = ( ! empty( $new_instance['randomize'] ) ) ? strip_tags( $new_instance['randomize'] ) : '';
		$instance['maxshown'] = ( ! empty( $new_instance['maxshown'] ) ) ? strip_tags( $new_instance['maxshown'] ) : '';
		return $instance;
	}
}

function ph_review_shcode($atts = [], $content = null, $tag = '') {
	$atts = array_change_key_case((array)$atts, CASE_LOWER);
	wp_enqueue_script( 'bxslider');
	wp_enqueue_script( 'powerhouseslider');
	wp_enqueue_style( 'bxslider-css');
	wp_enqueue_style( 'font-awesome');		
	$ph_atts = shortcode_atts([
								 'title' => 'GMG Testimonials',
								 'showstars' => true,
								 'content' => true,
								 'random' => true,
								 'maxshown' => 5
							 ], $atts, $tag);
	$title = apply_filters( 'widget_title', $ph_atts['title'] );
	$showstars = filter_var($ph_atts['showstars'], FILTER_VALIDATE_BOOLEAN);
	$contentshown = filter_var($ph_atts['content'], FILTER_VALIDATE_BOOLEAN);
	$randomize = filter_var($ph_atts['random'], FILTER_VALIDATE_BOOLEAN);
	$maxshown = $ph_atts['maxshown'];
	if ($randomize) {
		$randomize = 'rand';
	} else {
		$randomize = 'title';
	}
	$args = array(
		'post_type' => 'ph_testimonials_7839',
		'posts_per_page'=> $maxshown,
		'orderby' => $randomize,
		'order' => 'ASC'
	);
	$loop = new WP_Query( $args );
	if( $loop->have_posts() ) {
		?><h2><?php echo $title; ?></h2><ul class="bxslider powerhouse-slider"><?php
		while( $loop->have_posts() ): $loop->the_post();
			?>
			<li>
				<?php if ($showstars){ ?>
					<?php// $rating = get_post_meta( get_the_ID(), 'rating_value', true); ?>
					<?php $rating = get_field('rating_value'); ?>
					<?php if ($rating) { ?>
						<div class="powerhouse-stars">
						<?php while($rating > 0) {?>
							<i class="fa fa-star" aria-hidden="true"></i>
							<?php $rating--; ?>
							<?php $rating--; ?>
						<?php } ?>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if ($contentshown){ ?>
					<?php the_content(); ?>
				<?php } ?>
				<p><strong><?php echo get_the_title();?></strong></p>
			</li>
			<?php
		endwhile;
		?></ul><?php
	}
	wp_reset_query();
}
function wpse72394_shortcode_button_init() {
  if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	   return;
  add_filter("mce_external_plugins", "wpse72394_register_tinymce_plugin"); 
  add_filter('mce_buttons', 'wpse72394_add_tinymce_button');
}
function wpse72394_register_tinymce_plugin($plugin_array) {
    $plugin_array['wpse72394_button'] =  plugin_dir_url( __FILE__ ) . 'includes/js/powerhouseshocode.js';
    return $plugin_array;
}
function wpse72394_add_tinymce_button($buttons) {
    $buttons[] = "wpse72394_button";
    return $buttons;
}
function register_shortcodes(){
   add_shortcode('gmg-reviews', 'ph_review_shcode');
}
function ph_load_widget() {
	register_widget( 'ph_widget' );
}
function ph_static_load_widget() {
	register_widget( 'ph_static_widget' );
}

add_action('admin_init', 'wpse72394_shortcode_button_init'); //Generate TinyMCE Button
add_action( 'init', 'register_shortcodes'); //Generate Shortcode
add_action( 'widgets_init', 'ph_load_widget' ); //Generate Slider Widget
add_action( 'widgets_init', 'ph_static_load_widget' );  //Generate Static Widget
add_action( 'init', 'create_ph_ptype' ); //Generate Custom Post Type

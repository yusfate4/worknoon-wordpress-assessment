<?php
/**
 * Plugin Name: Worknoon Core Systems
 * Description: Registers Custom Post Types, Meta Boxes, and Shortcodes for the Worknoon Assessment.
 * Version: 1.1
 * Author: Yusuf Dahud
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 1. Register Services CPT
function worknoon_register_services_cpt() {
    register_post_type('service', array(
        'labels'      => array('name' => 'Services', 'singular_name' => 'Service'),
        'public'      => true,
        'menu_icon'   => 'dashicons-hammer',
        'supports'    => array('title', 'editor', 'thumbnail'),
    ));
}
add_action('init', 'worknoon_register_services_cpt');

// 2. Register Testimonials CPT
function worknoon_register_testimonials_cpt() {
    register_post_type('testimonial', array(
        'labels'      => array('name' => 'Testimonials', 'singular_name' => 'Testimonial'),
        'public'      => true,
        'menu_icon'   => 'dashicons-format-quote',
        'supports'    => array('title', 'editor', 'thumbnail'), // Added thumbnail for the Image
    ));
}
add_action('init', 'worknoon_register_testimonials_cpt');

// 3. Add Custom Meta Boxes for Job Title & Star Rating
function worknoon_add_testimonial_meta_boxes() {
    add_meta_box('testimonial_details', 'Testimonial Details', 'worknoon_render_testimonial_meta_box', 'testimonial', 'normal', 'high');
}
add_action('add_meta_boxes', 'worknoon_add_testimonial_meta_boxes');

function worknoon_render_testimonial_meta_box($post) {
    wp_nonce_field('worknoon_save_testimonial_data', 'worknoon_testimonial_meta_nonce');
    $job_title = get_post_meta($post->ID, '_testimonial_job_title', true);
    $rating = get_post_meta($post->ID, '_testimonial_rating', true);
    ?>
    <p>
        <label for="testimonial_job_title"><strong>Job Title / Company:</strong></label><br>
        <input type="text" id="testimonial_job_title" name="testimonial_job_title" value="<?php echo esc_attr($job_title); ?>" style="width:100%;">
    </p>
    <p>
        <label for="testimonial_rating"><strong>Star Rating (1-5):</strong></label><br>
        <input type="number" id="testimonial_rating" name="testimonial_rating" min="1" max="5" value="<?php echo esc_attr($rating); ?>">
    </p>
    <?php
}

function worknoon_save_testimonial_meta($post_id) {
    if (!isset($_POST['worknoon_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['worknoon_testimonial_meta_nonce'], 'worknoon_save_testimonial_data')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['testimonial_job_title'])) {
        update_post_meta($post_id, '_testimonial_job_title', sanitize_text_field($_POST['testimonial_job_title']));
    }
    if (isset($_POST['testimonial_rating'])) {
        update_post_meta($post_id, '_testimonial_rating', absint($_POST['testimonial_rating']));
    }
}
add_action('save_post_testimonial', 'worknoon_save_testimonial_meta');

// 4. Inject Google Analytics
function worknoon_add_google_analytics() {
    ?>
    <!-- Google Analytics Integration -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');
    </script>
    <?php
}
add_action('wp_head', 'worknoon_add_google_analytics');

// 5. Shortcode to Display Testimonials in Elementor
// 5. Shortcode to Display Testimonials in Elementor (Clean Classes)
function worknoon_testimonials_shortcode() {
    $args = array('post_type' => 'testimonial', 'posts_per_page' => 3);
    $query = new WP_Query($args);
    
    // Grid wrapper
    $output = '<div class="worknoon-testimonials-grid">';
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $job_title = get_post_meta(get_the_ID(), '_testimonial_job_title', true);
            $rating = get_post_meta(get_the_ID(), '_testimonial_rating', true);
            $stars = str_repeat('⭐', $rating ? $rating : 5);
            $image = get_the_post_thumbnail(get_the_ID(), 'thumbnail', array('class' => 'worknoon-author-img'));

            // Card wrapper
            $output .= '<div class="worknoon-testimonial-card">';
            
            $output .= '<div class="worknoon-stars">' . $stars . '</div>';
            $output .= '<div class="worknoon-quote">"' . get_the_content() . '"</div>';
            
            // Author section
            $output .= '<div class="worknoon-author-meta">';
            if ($image) $output .= '<div class="worknoon-image-wrapper">' . $image . '</div>';
            $output .= '<div class="worknoon-author-details">';
            $output .= '<strong>' . get_the_title() . '</strong><br>';
            $output .= '<span>' . esc_html($job_title) . '</span>';
            $output .= '</div>'; // close details
            
            $output .= '</div>'; // close meta
            $output .= '</div>'; // close card
        }
        wp_reset_postdata();
    }
    $output .= '</div>'; // close grid
    return $output;
}
add_shortcode('worknoon_testimonials', 'worknoon_testimonials_shortcode');

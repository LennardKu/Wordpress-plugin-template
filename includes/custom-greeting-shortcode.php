<?php
// Add a shortcode to display a custom greeting message
function custom_greeting_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(
        array(
            'name' => 'Guest',
        ),
        $atts
    );

    // Generate the greeting message
    $message = 'Hello, ' . sanitize_text_field($atts['name']) . '! Welcome to our website.';

    // Return the greeting message
    return $message;
}
add_shortcode('custom_greeting', 'custom_greeting_shortcode');

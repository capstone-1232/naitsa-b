<?php
// Enqueue styles
function dt_enqueue_styles() {
    $parenthandle = 'divi-style'; 
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(), // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') 
    );
}
add_action( 'wp_enqueue_scripts', 'dt_enqueue_styles' );

// Removing Gutenberg from menu custom post type
function remove_gutenberg_support() {
    remove_post_type_support( 'menu-item', 'editor' );
}
add_action( 'init', 'remove_gutenberg_support' );


// Create a function to display menu items
function display_menu_items() {
    // Query parameters
    $args = array(
        'post_type' => 'menu-item', // Your custom post type
        'posts_per_page' => -1, // Retrieve all posts of this type
        // Add any additional parameters you need
    );

    // Query the posts
    $query = new WP_Query($args);

    // Check if there are posts
    if ($query->have_posts()) {
        // Start the loop
        while ($query->have_posts()) {
            $query->the_post();

            // Display post title and content
            echo '<div class="menu-item-container">';
            echo '<div class="menu-flex-container">';
            echo '<h2>' . get_the_title() . '</h2>';
            $menu_item_price = get_field('menu_item_price'); // Replace 'your_custom_field_name' with the actual field name
            echo '<p>$' . $menu_item_price . '</p>';
            echo '</div>'; // menu-flex-container closing

            echo '<div>' . get_the_content() . '</div>';

            $menu_item_description = get_field('menu_item_description'); // Replace 'your_custom_field_name' with the actual field name
            echo '<p>' . $menu_item_description . '</p>';

            echo '</div>'; // menu-item-container closing
           


            
        }

        // Restore original post data
        wp_reset_postdata();
    } else {
        // If no posts found
        echo 'No menu items found.';
    }
}

// Add a shortcode to display menu items on a specific page
function menu_items_shortcode() {
    ob_start(); // Start output buffering
    display_menu_items(); // Call the function to display menu items
    return ob_get_clean(); // Return buffered content
}
add_shortcode('display_menu_items', 'menu_items_shortcode');

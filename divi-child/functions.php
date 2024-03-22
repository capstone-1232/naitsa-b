<?php
// Enqueue styles
function dt_enqueue_styles() {
    $parenthandle = 'divi-style'; 
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(), 
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


// function to display menu items
function display_menu_items() {
    $menu_categories = get_terms(array(
        'taxonomy' => 'menu-categories', // our menu category taxonomy slug
        'hide_empty' => false, 
    ));

    echo '<div class="category-links">';
    echo '<ul class="cat-list">';
    echo '<li><a class="cat-list-item active" href="#!" data-slug="">All</a></li>';

    foreach ($menu_categories as $menu_category) {

        echo '<li>';
        echo '<a class="cat-list-item" href="#" data-slug="' . $menu_category->slug . '">' . $menu_category->name . '</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';

    foreach ($menu_categories as $menu_category) {
        $args = array(
            'post_type' => 'menu-item', // our custom post type slug
            'posts_per_page' => -1, // -1 gets all the posts of this post type
            'tax_query' => array(
                array(
                    'taxonomy' => 'menu-categories', // our menu category taxonomy slug
                    'field' => 'slug',
                    'terms' => $menu_category->slug, // the current category slug
                ),
            ),
        );

        // query the posts
        $query = new WP_Query($args);

        // checking if there are posts
        if ($query->have_posts()) {
            echo '<div class="menu-category-' . $menu_category->slug . '">'; 
            echo '<h2>' . $menu_category->name . '</h2>'; 

            // loop
            while ($query->have_posts()) {
                $query->the_post();

                // display post title and content
                echo '<div class="menu-item-container">';
                echo '<div class="menu-flex-container">';
                echo '<h3>' . get_the_title() . '</h3>'; // title
                $menu_item_price = get_field('menu_item_price');
                echo '<p>$' . $menu_item_price . '</p>'; // price
                echo '</div>'; // menu-flex-container closing

                echo '<div>' . get_the_content() . '</div>'; 

                $menu_item_description = get_field('menu_item_description'); 
                echo '<p>' . $menu_item_description . '</p>'; // description

                echo '</div>'; // menu-item-container closing
            }

            echo '</div>'; // category container closing

            // restore original post data
            wp_reset_postdata();
        } else {
            // else no posts found for this category
            echo '<p>No menu items found for ' . $menu_category->name . '.</p>';
        }
    }
}

// display menu items shortcode
function menu_items_shortcode() {
    ob_start(); 
    display_menu_items(); 
    return ob_get_clean(); 
}
add_shortcode('display_menu_items', 'menu_items_shortcode');


function filter_menu() {
    $category_slug = $_POST['category'];
  
    $args = array(
        'post_type' => 'menu-item',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'menu-categories',
                'field' => 'slug',
                'terms' => $category_slug,
            ),
        ),
        'orderby' => 'menu_order',
        'order' => 'desc',
    );

    $ajaxposts = new WP_Query($args);
    $response = '';

    if($ajaxposts->have_posts()) {
        while($ajaxposts->have_posts()) : $ajaxposts->the_post();
            // Output HTML directly here
            $response .= '<div class="menu-item-container">';
            $response .= '<div class="menu-flex-container">';
            $response .= '<h3>' . get_the_title() . '</h3>'; // title
            $menu_item_price = get_field('menu_item_price');
            $response .= '<p>$' . $menu_item_price . '</p>'; // price
            $response .= '</div>'; // menu-flex-container closing
            $response .= '<div>' . get_the_content() . '</div>'; 
            $menu_item_description = get_field('menu_item_description'); 
            $response .= '<p>' . $menu_item_description . '</p>'; // description
            $response .= '</div>'; // menu-item-container closing
        endwhile;
    } else {
        $response = 'empty';
    }

    echo $response;
    exit;
}

add_action('wp_ajax_filter_menu', 'filter_menu');
add_action('wp_ajax_nopriv_filter_menu', 'filter_menu');




// function to dispay events - Gurpreet singh

// Create a function to display events with filtering
function display_events($search_query = '', $date = '', $month = '') {
    // Prepare arguments for WP_Query
    $args = array(
        'post_type'      => 'events_page', // Your custom post type name
        'posts_per_page' => -1, // Display all events
        'order'          => 'ASC', // Order events by ascending order
    );

    // Add filter by date
    if (!empty($date)) {
        $args['meta_query'] = array(
            array(
                'key'     => 'event_date_time',
                'value'   => $date,
                'compare' => '=',
                'type'    => 'DATE',
            ),
        );
    }

    // Add filter by month
    if (!empty($month)) {
        $args['meta_query'] = array(
            array(
                'key'     => 'event_date_time',
                'value'   => array(date('Y-m-01', strtotime($month)), date('Y-m-t', strtotime($month))),
                'compare' => 'BETWEEN',
                'type'    => 'DATE',
            ),
        );
    }

    // Add search query
    if (!empty($search_query)) {
        $args['s'] = $search_query;
    }

    // Query events
    $events_query = new WP_Query($args);

    // Check if there are any events
    if ($events_query->have_posts()) {
        // Start the loop
        while ($events_query->have_posts()) {
            $events_query->the_post();
            ?>
<div class="event">
    <h2><?php the_field('event_heading'); ?></h2>
    <div class="event-image">
        <?php $event_image = get_field('event_image'); ?>
        <?php if ($event_image) : ?>
        <img src="<?php echo esc_url($event_image['url']); ?>" alt="<?php echo esc_attr($event_image['alt']); ?>">
        <?php endif; ?>
    </div>
    <div class="event-description">
        <?php the_field('event_description'); ?>
    </div>
    <div class="event-date-time">
        <?php echo date('F j, Y', strtotime(get_field('event_date_time'))); ?>
    </div>
    <div class="event-link">
        <a href="<?php the_field('event_link'); ?>" target="_blank">Event Link</a>
    </div>
</div>
<?php
        }
        // Reset Post Data
        wp_reset_postdata();
    } else {
        // If no events are found
        echo '<p>No events found.</p>';
    }
}

// Shortcode function for displaying events
function events_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'search_query' => '', // Search query
        'date'         => '', // Date to filter events (YYYY-MM-DD)
        'month'        => '', // Month to filter events (YYYY-MM)
    ), $atts);

    // Start output buffering
    ob_start(); 

    // Display events with provided filters
    display_events($atts['search_query'], $atts['date'], $atts['month']); 

    // Return the buffered content
    return ob_get_clean(); 
}
add_shortcode('display_events', 'events_shortcode');
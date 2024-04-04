<?php
// Enqueue styles
function dt_enqueue_styles()
{
    $parenthandle = 'divi-style';
    $theme = wp_get_theme();
    wp_enqueue_style(
        $parenthandle,
        get_template_directory_uri() . '/style.css',
        array(),
        $theme->parent()->get('Version')
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        array($parenthandle),
        $theme->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'dt_enqueue_styles');

// function to remove gutenberg editor from CPT
function remove_gutenberg_support()
{
    remove_post_type_support('menu-item', 'editor');
    remove_post_type_support('events_page', 'editor');
}
add_action('init', 'remove_gutenberg_support');


// function to display menu items
function display_menu_items()
{
    $menu_categories = get_terms(array(
        'taxonomy' => 'menu-categories', // our menu category taxonomy slug
        'hide_empty' => false,
    ));
?>

    <div class="category-links">
        <ul class="cat-list">
            <li><a class="cat-list-item" href="#" data-slug="">All</a></li>

            <?php foreach ($menu_categories as $menu_category) : ?>
                <?php if (empty(!get_term_children($menu_category->term_id, 'menu-categories')) || $menu_category->parent === 0) : ?>

                    <li>
                        <a class="cat-list-item" href="#" data-slug="<?php echo $menu_category->slug; ?>"><?php echo $menu_category->name; ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>


    <?php
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
    ?>

            <div class="menu-category menu-category-<?php echo $menu_category->slug; ?>">
                <h2><?php echo $menu_category->name; ?></h2>

                <?php
                // loop
                while ($query->have_posts()) {
                    $query->the_post();

                    $menu_item_price = get_field('menu_item_price');
                    $menu_item_addon_name = get_field('add_on_name_1');
                    $menu_item_addon_price = get_field('add_on_price_1');
                    $menu_item_photo = get_field('menu_item_photo');
                    $dietary_options = get_field('dietary_options');

                ?>
                    <div class="menu-item-container">
                        <div class="menu-text-container">
                            <div class="menu-flex-container">
                                <div class="flex-row">
                                    <h3><?php echo get_the_title(); ?></h3> <!-- title -->
                                    <?php

                                    foreach ($dietary_options as $option) {
                                        switch ($option) {
                                            case 'Gluten Friendly':
                                                $gluten_attachment_id = 626;
                                                $gluten_icon_url = wp_get_attachment_url($gluten_attachment_id);
                                                echo '<img src="' . esc_url($gluten_icon_url) . '" alt="Gluten Friendly Icon" class="dietary-icon" width="20" height="auto">';

                                                break;
                                            case 'Vegetarian':
                                                $vegetarian_attachment_id = 632;
                                                $vegetarian_icon_url = wp_get_attachment_url($vegetarian_attachment_id);
                                                echo '<img src="' . esc_url($vegetarian_icon_url) . '" alt="Vegetarian Icon" class="dietary-icon" width="20" height="auto">';
                                                break;
                                            case 'Spicy':
                                                $spicy_attachment_id = 631;
                                                $spicy_icon_url = wp_get_attachment_url($spicy_attachment_id);
                                                echo '<img src="' . esc_url($spicy_icon_url) . '" alt="Spicy Icon" class="dietary-icon" width="20" height="auto">';
                                                break;
                                        }
                                    }
                                    ?>
                                </div>
                                <p>$<?php echo $menu_item_price; ?></p> <!-- price -->

                            </div> <!-- menu-flex-container closing -->

                            <?php

                            for ($i = 1; $i <= 5; $i++) {
                                $addon_name = get_field('add_on_name_' . $i);
                                $addon_price = get_field('add_on_price_' . $i);

                                if ($addon_name && $addon_price) { ?>

                                    <div class="menu-addon-container">
                                        <p><?php echo $addon_name; ?></p> <!-- addon name -->
                                        <p>$<?php echo $addon_price; ?></p> <!-- addon price -->
                                    </div>
                            <?php
                                }
                            } ?>



                            <div><?php echo get_the_content(); ?></div>
                            <p><?php echo $menu_item_description = get_field('menu_item_description'); ?></p> <!-- description -->
                        </div>
                        <div class="menu-photo-container">
                            <?php if ($menu_item_photo) : ?>
                                <img src="<?php echo $menu_item_photo['url']; ?>" alt="<?php echo $menu_item_photo['alt']; ?>" class="menu-item-photo" width="100" height="auto">
                            <?php endif; ?>
                        </div>
                    </div>

                <?php
                } ?>

            </div> <!-- category container closing -->



        <?php
            // restore original post data
            wp_reset_postdata();
        } else {
            // else no posts found for this category
        ?>

            <p>No menu items found for <?php echo $menu_category->name ?>.</p>

        <?php
        }
    }
}

// display menu items shortcode
function menu_items_shortcode()
{
    ob_start();
    display_menu_items();
    return ob_get_clean();
}
add_shortcode('display_menu_items', 'menu_items_shortcode');


function filter_menu()
{
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

    if ($ajaxposts->have_posts()) {
        while ($ajaxposts->have_posts()) : $ajaxposts->the_post();

            $menu_item_addons = get_field('add_ons');

            $response .= '<div class="menu-item-container">';
            $response .= '<div class="menu-flex-container">';
            $response .= '<h3>' . get_the_title() . '</h3>'; // title
            $menu_item_price = get_field('menu_item_price');
            $response .= '<p>$' . $menu_item_price . '</p>'; // price
            $response .= '</div>'; // menu-flex-container closing

            if ($menu_item_addons) {
                $response .= '<div class="menu-addon-container">'; // addon container start

                // Loop through addon fields dynamically
                for ($i = 1; $i <= 5; $i++) {
                    $addon_name_key = 'add_on_name_' . $i;
                    $addon_price_key = 'add_on_price_' . $i;

                    // Check if addon name and price exist
                    $addon_name = get_field($addon_name_key);
                    $addon_price = get_field($addon_price_key);

                    if ($addon_name && $addon_price) {
                        // Display addon name and price
                        $response .= '<p>' . $addon_name . '</p>'; // addons
                        $response .= '<p>' . $addon_price . '</p>'; // addons
                    }
                }

                $response .= '</div>'; // menu-addon-container closing
            }

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

function enqueue_menu_filter_script()
{
    wp_enqueue_script('jquery');

    wp_enqueue_script('menu-filter-script', get_stylesheet_directory_uri() . '/menu-filter.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_menu_filter_script');


function display_weekly_specials()
{
    // current day of the week
    $current_day = strtolower(date('l')); // returns the lowercase full name of the day (e.g., monday)

    $args = array(
        'post_type' => 'menu-item',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'menu-categories',
                'field' => 'slug',
                'terms' => $current_day, // current day's slug as the term to query
            ),
        ),
    );

    $weekly_specials_query = new WP_Query($args);

    ob_start();

    if ($weekly_specials_query->have_posts()) {

        ?>
        <div class="weekly-specials">
            <h2>Today's Specials</h2>
            <div class="specials-container">
                <div class="specials-content">
                    <ul>
                        <?php while ($weekly_specials_query->have_posts()) : $weekly_specials_query->the_post(); ?>
                            <li><?php the_title(); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div> <!-- .specials-content -->
            </div> <!-- .specials-container -->
        </div> <!-- .weekly-specials -->
    <?php
    } else {
    ?>
        <div class="weekly-specials">
            <h2>Today's Specials</h2>
            <div class="specials-container">
                <div class="specials-content">
                    <p>No specials found for today.</p>
                </div> <!-- .specials-content -->
            </div> <!-- .specials-container -->
        </div> <!-- .weekly-specials -->
        <?php
    }

    wp_reset_postdata();

    echo ob_get_clean();
}

function weekly_specials_shortcode()
{
    ob_start();
    display_weekly_specials();
    return ob_get_clean();
}
add_shortcode('display_weekly_specials', 'weekly_specials_shortcode');

// function to dispay events - Gurpreet singh

function display_events($search_query = '', $date = '', $month = '')
{
    // Prepare arguments for WP_Query
    $args = array(
        'post_type'      => 'events_page',
        'posts_per_page' => -1,
        'order'          => 'ASC',
    );


    $meta_query = array();

    // Add filter by date
    if (!empty($date)) {
        $meta_query[] = array(
            'key'     => 'event_date_time',
            'value'   => $date,
            'compare' => '=',
            'type'    => 'DATE',
        );
    }


    if (!empty($month)) {
        $meta_query[] = array(
            'key'     => 'event_date_time',
            'value'   => array(date('Y-m-01', strtotime($month)), date('Y-m-t', strtotime($month))),
            'compare' => 'BETWEEN',
            'type'    => 'DATE',
        );
    }


    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }


    if (!empty($search_query)) {
        $args['s'] = $search_query;
    }


    $events_query = new WP_Query($args);

    // check if there are any events
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
                <div class="event_location">
                    <?php the_field('event_location'); ?>
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

        wp_reset_postdata();
    } else {

        echo '<p>No events found.</p>';
    }
}


// shortcode function for displaying events with search form
function events_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'date'  => '', // Date to filter events (YYYY-MM-DD)
        'month' => '', // Month to filter events (YYYY-MM)
    ), $atts);

    ob_start();
    ?>
    <div class="events-search">
        <form role="search" method="get" class="search-form" id="events-search-form">
            <label>
                <span class="screen-reader-text"><?php _e('Search for:', 'textdomain'); ?></span>
                <input type="search" class="search-field" id="events-search-input" placeholder="<?php _e('Search events', 'textdomain'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" name="search" title="<?php _e('Search for:', 'textdomain'); ?>" />
            </label>
            <button type="submit" class="search-submit"><span class="screen-reader-text"><?php _e('Search', 'textdomain'); ?></span>Search</button>
        </form>
    </div>
    <div id="events-results">
        <?php
        // display events with provided filters and search query
        display_events(isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '', $atts['date'], $atts['month']);
        ?>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('#events-search-form').on('submit', function(e) {
                e.preventDefault(); // prevent form submission

                var formData = $(this).serialize(); // serialize form data
                $.ajax({
                    type: 'GET',
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>', // URL to handle the AJAX request
                    data: formData + '&action=events_search', // Add action parameter
                    success: function(response) {
                        $('#events-results').html(
                            response); // Update results container with AJAX response
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('display_events', 'events_shortcode');

// AJAX handler for search
add_action('wp_ajax_events_search', 'events_search_ajax_handler');
add_action('wp_ajax_nopriv_events_search', 'events_search_ajax_handler');
function events_search_ajax_handler()
{

    $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    display_events($search_query);

    exit();
}

function auto_select_parent_category($post_id)
{
    // Get the post object
    $post = get_post($post_id);

    // Check if the post is a menu item
    if ($post->post_type === 'menu-item') {
        // Get the post terms
        $terms = get_the_terms($post_id, 'menu-categories');

        // Check if the post has terms
        if ($terms) {
            // Get the first term
            $term = array_shift($terms);

            // Get the parent term
            $parent_term = get_term($term->parent, 'menu-categories');

            // Check if the parent term is not empty
            if ($parent_term) {
                // Set both parent and child terms
                $selected_terms = array($term->term_id, $parent_term->term_id);

                // Set the terms for the post
                wp_set_post_terms($post_id, $selected_terms, 'menu-categories');
            }
        }
    }
}

// Hook into save_post action
add_action('save_post', 'auto_select_parent_category');


// home page event card display most recent event

function display_most_recent_event()
{
    $args = array(
        'post_type'      => 'events_page',
        'posts_per_page' => 1,
        'order'          => 'DESC',
        'orderby'        => 'date',
    );

    $most_recent_event_query = new WP_Query($args);

    if ($most_recent_event_query->have_posts()) {
        while ($most_recent_event_query->have_posts()) {
            $most_recent_event_query->the_post();
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
                <div class="event-link
    ">
                    <a href="<?php the_field('event_link'); ?>" target="_blank">Event Link</a>
                </div>
            </div>
<?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No events found.</p>';
    }
}

function most_recent_event_shortcode()
{
    ob_start();
    display_most_recent_event();
    return ob_get_clean();
}

add_shortcode('display_most_recent_event', 'most_recent_event_shortcode');

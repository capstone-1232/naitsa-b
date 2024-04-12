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
    remove_post_type_support('seasonal_promotion', 'editor');
}
add_action('init', 'remove_gutenberg_support');

// function et_add_viewport_meta(){
// 	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
// }

// add_action( 'wp_head', 'et_add_viewport_meta' );

add_action('after_setup_theme', 'remove_parent_theme_function', 11);
function remove_parent_theme_function()
{
    remove_action('wp_head', 'et_add_viewport_meta');
}


// function to add image to menu-categories taxonomy


// function to display menu items


function display_menu_items()
{

    $menu_categories = get_terms(
        array(
            'taxonomy' => 'menu-categories', // our menu category taxonomy slug
            'hide_empty' => false,
            'parent' => 0, // Only get top-level categories
        )
    );
?>

<section class="splide" aria-label="Splide Basic HTML Example">
    <div class="category-links splide__track">
        <ul class="cat-list splide__list">
            <li class="splide__slide"><a class="cat-list-item" href="#" data-slug="">All</a></li>

            <?php foreach ($menu_categories as $menu_category) : ?>
                <?php if (empty(!get_term_children($menu_category->term_id, 'menu-categories')) || $menu_category->parent === 0) : ?>

                    <?php
                    $category_image = get_field('category_image', $menu_category);
                    ?>

                    <li class="splide__slide">
                        <a class="cat-list-item" href="#" data-slug="<?php echo $menu_category->slug; ?>">
                            <?php if ($category_image) : ?>
                                <img src="<?php echo $category_image; ?>" alt="<?php echo $menu_category->name; ?>">
                            <?php endif; ?>

                        </a>

                        <a class="cat-list-item" href="#" data-slug="<?php echo $menu_category->slug; ?>">
                            <?php echo $menu_category->name; ?>
                        </a>


                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var splide = new Splide('.splide', {
             drag: 'free',
             snap: 'true',
             omitEnd: true,
             arrows: false,
             perPage: 10,
             slideFocus: true,
             flickPower: 500,
             width: 900,
             breakpoints: {
                1000: {
                    perMove: 0
                }
                900: {
                    perPage: 7,
                    width: 900
                },
                800: {
                    perPage: 6,
                    width: 800
                },
                700: {
                    perPage: 5,
                    width: 700
                },
                 600: {
                     perPage: 4, 
                 },
                 450: {
                     perPage: 3, 
                 }
                
             }
        }).mount();

     });
</script>

    <?php

    // Loop through top-level categories
    foreach ($menu_categories as $menu_category) {
    ?>
        <div class="menu-category menu-category-<?php echo $menu_category->slug; ?>">
            <h2>
                <?php echo $menu_category->name; ?>
            </h2>
            <div class="menu-two-row">
                <?php
                // Get subcategories of current top-level category
                $subcategories = get_terms(
                    array(
                        'taxonomy' => 'menu-categories',
                        'hide_empty' => false,
                        'parent' => $menu_category->term_id, // Get subcategories of current category
                    )
                );

                // If there are subcategories, display them
                if (!empty($subcategories)) {
                    // Loop through subcategories
                    foreach ($subcategories as $subcategory) {
                        $args = array(
                            'post_type' => 'menu-item', // our custom post type slug
                            'posts_per_page' => -1, // -1 gets all the posts of this post type
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'menu-categories', // our menu category taxonomy slug
                                    'field' => 'slug',
                                    'terms' => $subcategory->slug, // the current subcategory slug
                                ),
                            ),
                        );

                        // query the posts
                        $query = new WP_Query($args);

                        // checking if there are posts
                        if ($query->have_posts()) {
                ?>
                            <div class="sub-menu-category sub-menu-category-<?php echo $subcategory->slug; ?>">
                                <h3>
                                    <?php echo $subcategory->name; ?>
                                </h3>
                                <div class="menu-two-row">
                                <?php
                                // loop
                                while ($query->have_posts()) {
                                    $query->the_post();
                                    $menu_item_price = get_field('menu_item_price');
                                    $menu_item_addon_name = get_field('add_on_name_1');
                                    $menu_item_addon_price = get_field('add_on_price_1');
                                    $menu_item_photo = get_field('menu_item_photo');
                                    $dietary_options = get_field('dietary_options');
                                    $parent_term = get_term($menu_category->term_id);
                                ?>

                                    <div class="menu-item-container">
                                        <div class="menu-text-container">
                                            <div class="menu-flex-container">
                                                <div>
                                                    <h3 class="dish-name">
                                                        <?php echo get_the_title(); ?>
                                                    </h3> <!-- title -->
                                                    <?php
                                                    foreach ($dietary_options as $option) {
                                                        switch ($option) {
                                                            case 'Gluten Friendly':
                                                                $gluten_attachment_id = 626;
                                                                $gluten_icon_url = wp_get_attachment_url($gluten_attachment_id);
                                                                echo '<img src="' . esc_url($gluten_icon_url) . '" alt="Gluten Friendly Icon" class="dietary-icon">';
                                                                break;
                                                            case 'Vegetarian':
                                                                $vegetarian_attachment_id = 632;
                                                                $vegetarian_icon_url = wp_get_attachment_url($vegetarian_attachment_id);
                                                                echo '<img src="' . esc_url($vegetarian_icon_url) . '" alt="Vegetarian Icon" class="dietary-icon">';
                                                                break;
                                                            case 'Spicy':
                                                                $spicy_attachment_id = 631;
                                                                $spicy_icon_url = wp_get_attachment_url($spicy_attachment_id);
                                                                echo '<img src="' . esc_url($spicy_icon_url) . '" alt="Spicy Icon" class="dietary-icon">';
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <p class="dish-price">$
                                                    <?php echo $menu_item_price; ?>
                                                </p> <!-- price -->
                                            </div> <!-- menu-flex-container closing -->

                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                $addon_name = get_field('add_on_name_' . $i);
                                                $addon_price = get_field('add_on_price_' . $i);
                                                if ($addon_name && $addon_price) {
                                            ?>
                                                    <div class="menu-addon-container">
                                                        <p class="addon-name">
                                                            <?php echo $addon_name; ?>
                                                        </p> <!-- addon name -->
                                                        <p class="addon-price">$
                                                            <?php echo $addon_price; ?>
                                                        </p> <!-- addon price -->
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>


                                            <p class="dish-description" ><?php echo $menu_item_description = get_field('menu_item_description'); ?></p>
                                            <!-- description -->
                                        </div>
                                        <?php if ($menu_item_photo) : ?>
                                            <img src="<?php echo $menu_item_photo['url']; ?>" alt="<?php echo $menu_item_photo['alt']; ?>" class="menu-item-photo" width="200" height="auto">
                                        <?php else : ?>
                                            <?php $img_placeholder = wp_get_attachment_url(983); ?>
                                            <img src="<?php echo esc_url($img_placeholder); ?>" alt="Placeholder" class="menu-item-photo" width="200" height="auto">
                                        <?php endif; ?>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            </div>
                        <?php
                            // Restore original post data
                            wp_reset_postdata();
                        } else {
                            // No menu items found for this subcategory
                        ?>
                            <p>No menu items found for <?php echo $subcategory->name ?>.</p>
                        <?php
                        }
                    }
                } else {
                    // If there are no subcategories, display menu items directly under the parent category
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
                        // loop
                        while ($query->have_posts()) {
                            $query->the_post();
                            $menu_item_price = get_field('menu_item_price');
                            $menu_item_addon_name = get_field('add_on_name_1');
                            $menu_item_addon_price = get_field('add_on_price_1');
                            $menu_item_photo = get_field('menu_item_photo');
                            $dietary_options = get_field('dietary_options');
                            $parent_term = get_term($menu_category->term_id);
                        ?>
                            <div class="menu-item-container">
                                <div class="menu-text-container">
                                    <div class="menu-flex-container">
                                        <div>
                                            <h3 class="dish-name">
                                                <?php echo get_the_title(); ?>
                                            </h3> <!-- title -->
                                            <?php
                                            foreach ($dietary_options as $option) {
                                                switch ($option) {
                                                    case 'Gluten Friendly':
                                                        $gluten_attachment_id = 626;
                                                        $gluten_icon_url = wp_get_attachment_url($gluten_attachment_id);
                                                        echo '<img src="' . esc_url($gluten_icon_url) . '" alt="Gluten Friendly Icon" class="dietary-icon">';
                                                        break;
                                                    case 'Vegetarian':
                                                        $vegetarian_attachment_id = 632;
                                                        $vegetarian_icon_url = wp_get_attachment_url($vegetarian_attachment_id);
                                                        echo '<img src="' . esc_url($vegetarian_icon_url) . '" alt="Vegetarian Icon" class="dietary-icon">';
                                                        break;
                                                    case 'Spicy':
                                                        $spicy_attachment_id = 631;
                                                        $spicy_icon_url = wp_get_attachment_url($spicy_attachment_id);
                                                        echo '<img src="' . esc_url($spicy_icon_url) . '" alt="Spicy Icon" class="dietary-icon">';
                                                        break;
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p class="dish-price">$
                                            <?php echo $menu_item_price; ?>
                                        </p> <!-- price -->
                                    </div> <!-- menu-flex-container closing -->

                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $addon_name = get_field('add_on_name_' . $i);
                                        $addon_price = get_field('add_on_price_' . $i);
                                        if ($addon_name && $addon_price) {
                                    ?>
                                            <div class="menu-addon-container">
                                                <p class="addon-name">
                                                    <?php echo $addon_name; ?>
                                                </p> <!-- addon name -->
                                                <p class="addon-price">$
                                                    <?php echo $addon_price; ?>
                                                </p> <!-- addon price -->
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>

                                    <div><?php echo get_the_content(); ?></div>
                                    <p class="dish-description"><?php echo $menu_item_description = get_field('menu_item_description'); ?>
                                    </p>
                                    <!-- description -->
                                </div>
                                <?php if ($menu_item_photo) : ?>
                                    <img src="<?php echo $menu_item_photo['url']; ?>" alt="<?php echo $menu_item_photo['alt']; ?>" class="menu-item-photo" width="200" height="auto">
                                <?php else : ?>

                                    <?php $img_placeholder = wp_get_attachment_url(983); ?>

                                    <img src="<?php echo esc_url($img_placeholder); ?>" alt="Placeholder" class="menu-item-photo" width="200" height="auto">
                                <?php endif; ?>
                            </div> <!-- menu item container closed -->
                        <?php
                        }
                        // Restore original post data
                        wp_reset_postdata();
                    } else {
                        // No menu items found for this category
                        ?>
                        <p>No menu items found for
                            <?php echo $menu_category->name ?>.
                        </p>
                <?php
                    }
                }
                ?>
            </div>
        </div> <!-- category container closing -->
    <?php
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
        while ($ajaxposts->have_posts()) :
            $ajaxposts->the_post();

            $menu_item_addons = get_field('add_ons');

            $response .= '<div class="menu-item-container">';
            $response .= '<div class="menu-flex-container">';
            $response .= '<h3 class="dish-name">' . get_the_title() . '</h3>'; // title
            $menu_item_price = get_field('menu_item_price');
            $response .= '<p class="dish-price">$' . $menu_item_price . '</p>'; // price
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
                        $response .= '<p class="addon-name">' . $addon_name . '</p>'; // addons
                        $response .= '<p class="addon-price>' . $addon_price . '</p>'; // addons
                    }
                }

                $response .= '</div>'; // menu-addon-container closing
            }

            $response .= '<div>' . get_the_content() . '</div>';
            $menu_item_description = get_field('menu_item_description');
            $response .= '<p class="dish-description">' . $menu_item_description . '</p>'; // description
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

    // if ($weekly_specials_query->have_posts()) { // UNCOMMENT THIS IF YOU DON'T WANT CARD TO SHOW ON SAT/SUN

    ?>
    <div class="weekly-specials">
        <div class="specials-container">
            <div class="specials-content">

                <?php if ($weekly_specials_query->have_posts()) : ?>
                    <h2><?php echo ucfirst($current_day); ?>'s Specials</h2>
                    <!-- <div class="specials-flex"> -->
                    <?php $image_displayed = false; ?>
                    <?php while ($weekly_specials_query->have_posts()) : $weekly_specials_query->the_post(); ?>
                        <?php if (!$image_displayed) : ?>
                            <?php $menu_item_photo_array = get_field('menu_item_photo'); ?>
                            <?php if ($menu_item_photo_array) : ?>
                                <div class="special-thumbnail">
                                    <img src="<?php echo esc_url($menu_item_photo_array['url']); ?>" alt="<?php echo esc_attr($menu_item_photo_array['alt']); ?>" width="150" height="auto">
                                </div>
                                <?php $image_displayed = true; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endwhile; ?>
                    <ul>
                        <?php while ($weekly_specials_query->have_posts()) : $weekly_specials_query->the_post(); ?>
                            <li>
                                <?php the_title(); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <!-- </div> -->
                <?php else : ?>
                    <h2>Weekly Specials</h2>
                    <ul>
                        <li>Check out our popular dishes for the day!</li>
                        <li><a href="<?php echo home_url('/menu/'); ?>">See More</a></li>
                    </ul>
                <?php endif; ?>
            </div> <!-- .specials-content -->
        </div> <!-- .specials-container -->
    </div> <!-- .weekly-specials -->


    <?php
}

wp_reset_postdata();

echo ob_get_clean();
// } // END OF IF STATMENT

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
        'post_type' => 'events_page',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'date',
        'meta_key' => 'event_date_time',
        'orderby' => 'meta_value',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'event_date_time',
                'value' => date('Y-m-d'), // Current date
                'compare' => '>=', // Greater than or equal to
                'type' => 'DATE',
            ),
        ),
    );

    // Add filter by specific date if provided
    if (!empty($date)) {
        $args['meta_query'][] = array(
            'key' => 'event_date_time',
            'value' => $date,
            'compare' => '=',
            'type' => 'DATE',
        );
    }

    // Add filter by specific month if provided
    if (!empty($month)) {
        $args['meta_query'][] = array(
            'key' => 'event_date_time',
            'value' => array(date('Y-m-01', strtotime($month)), date('Y-m-t', strtotime($month))),
            'compare' => 'BETWEEN',
            'type' => 'DATE',
        );
    }

    // Add search query if provided
    if (!empty($search_query)) {
        $args['s'] = $search_query;
    }

    // Query events
    $events_query = new WP_Query($args);

    // Check if there are any events
    if ($events_query->have_posts()) {
        echo '<div class="event-card-container">';
        // Start the loop
        while ($events_query->have_posts()) {
            $events_query->the_post();
    ?>
            <div class="event-card">
                <div class="event-image">
                    <?php $event_link = get_field('event_link'); ?>
                    <?php if ($event_link) : ?>
                        <a href="<?php echo esc_url($event_link); ?>" target="_blank">
                        <?php endif; ?>
                        <?php $event_image = get_field('event_image'); ?>
                        <?php if ($event_image) : ?>
                            <img src="<?php echo esc_url($event_image['url']); ?>" alt="<?php echo esc_attr($event_image['alt']); ?>">
                        <?php endif; ?>
                        <?php if ($event_link) : ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="event-details">
                    <div class="event-heading">
                        <h2><?php the_field('event_heading'); ?></h2>
                    </div>
                    <div class="event-date-time">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/img/calendar-icon.svg'; ?>" alt="Event Date Icon">
                        <?php
                        // Get the event date and time
                        $event_datetime = get_field('event_date_time');
                        // Format date and time
                        $formatted_datetime = date('F j, Y - g:i A', strtotime($event_datetime));
                        echo esc_html($formatted_datetime);
                        ?>
                    </div>
                    <div class="event-location">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/img/location-icon.svg'; ?>" alt="Event Location Icon">
                        <?php the_field('event_location'); ?>
                    </div>
                </div>
                <div class="event-link">
                    <a href="<?php the_field('event_link'); ?>" target="_blank">More Details on OoksLife</a>
                </div>
            </div>
    <?php
        }
        echo '</div>'; // Close event-card-container

        // Reset post data
        wp_reset_postdata();
    } else {
        echo '<p>No events found.</p>';
    }
}


//  Shortcode function for displaying events with search form
function events_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'date' => '',
            'month' => '',
        ),
        $atts
    );

    ob_start();
    ?>
    <div class="events-search">
        <form role="search" method="get" class="search-form" id="events-search-form">
            <label>
                <span class="screen-reader-text">
                    <?php _e('Search for:', 'textdomain'); ?>
                </span>
                <input type="search" class="search-field" id="events-search-input" placeholder="<?php _e('Search Events', 'textdomain'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" name="search" title="<?php _e('Search for:', 'textdomain'); ?>" />
            </label>
            <label for="month-filter">
                <span class="screen-reader-text">
                    <?php _e('Filter by Month:', 'textdomain'); ?>
                </span>
            </label>
            <select name="month" id="month-filter">
                <option value="">Filter by Months</option>
                <?php

                for ($i = 1; $i <= 12; $i++) {
                    $month_value = date('Y-m', mktime(0, 0, 0, $i, 1));
                    $month_label = date('F', mktime(0, 0, 0, $i, 1));
                    echo '<option value="' . $month_value . '"';
                    // Check if the current month is selected
                    if ($month_value === $atts['month']) {
                        echo ' selected';
                    }
                    echo '>' . $month_label . '</option>';
                }
                ?>
            </select>
            <button type="submit" class="search-submit"><span class="screen-reader-text">
                    <?php _e('Search', 'textdomain'); ?>
                </span>Search</button>
        </form>
    </div>
    <div id="events-results">
        <?php

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
// Add this code in your theme's functions.php file or in a custom plugin
add_action('wp_ajax_events_search', 'events_search');
add_action('wp_ajax_nopriv_events_search', 'events_search');

function events_search()
{
    $search_query = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
    $month = isset($_GET['month']) ? sanitize_text_field($_GET['month']) : '';

    // Display events based on search query, date, and month
    display_events($search_query, $date, $month);

    wp_die(); // Always include this to terminate script execution
}

// home page event card display most recent event

function display_most_recent_event()
{
    $args = array(
        'post_type' => 'events_page',
        'posts_per_page' => 1,
        'order' => 'DESC',
        'orderby' => 'date',
    );

    $most_recent_event_query = new WP_Query($args);

    if ($most_recent_event_query->have_posts()) {
        while ($most_recent_event_query->have_posts()) {
            $most_recent_event_query->the_post();
    ?>
            <div class="event-card-homepage">
                <h2>
                    <?php the_field('event_heading'); ?>
                </h2>
                <div class="event-image">
                    <?php $event_image = get_field('event_image'); ?>
                    <?php if ($event_image) : ?>
                        <img src="<?php echo esc_url($event_image['url']); ?>" alt="<?php echo esc_attr($event_image['alt']); ?>">
                    <?php endif; ?>
                </div>

                <div class="event-details">

                    <div class="event-date-time">
                        <p>On <?php echo date('F j, Y', strtotime(get_field('event_date_time'))); ?></p>
                    </div>
                    <div class="event_location">
                        <p>At <?php echo the_field('event_location'); ?></p>
                    </div>

                    <!-- <div class="event-link">
                        <a href="<?php // the_field('event_link'); ?>" target="_blank">Event Link</a>
                    </div> -->
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


// seasonal promotions
function display_seasonal_promotions()
{
    // Get current date
    $current_date = date('Y-m-d');

    // Prepare arguments for WP_Query
    $args = array(
        'post_type' => 'seasonal_promotion',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'promotion_start_date',
                'value' => $current_date,
                'compare' => '<=',
                'type' => 'DATE',
            ),
            array(
                'key' => 'promotion_end_date',
                'value' => $current_date,
                'compare' => '>=',
                'type' => 'DATE',
            ),
        ),
    );

    // Query seasonal promotions
    $promotions_query = new WP_Query($args);

    // Check if there are any promotions
    if ($promotions_query->have_posts()) {
        // Start the loop
        while ($promotions_query->have_posts()) {
            $promotions_query->the_post();
        ?>
            <div class="promotion-card">
                <h2>
                    <?php the_title(); ?>
                </h2>
                <div class="promotion-image">
                    <?php
                    $promotion_image = get_field('promotion_image');
                    if ($promotion_image) : ?>
                        <img src="<?php echo esc_url($promotion_image['url']); ?>" alt="<?php echo esc_attr($promotion_image['alt']); ?>">
                    <?php endif; ?>
                </div>
                <div class="promotion-description">
                    <p><?php echo the_field('promotion_description'); ?></p>
                </div>
            </div>
<?php
        }

        // Reset post data
        wp_reset_postdata();
    }
}

// Shortcode function for displaying seasonal promotions
function seasonal_promotions_shortcode()
{
    ob_start();
    display_seasonal_promotions();
    return ob_get_clean();
}

add_shortcode('display_seasonal_promotions', 'seasonal_promotions_shortcode');

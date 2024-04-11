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

// function for menu slider
function enqueue_swiper_scripts()
{
    // Enqueue Swiper CSS
    wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css');

    // Enqueue Swiper JS
    wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), false, true);

    // Enqueue your custom script that initializes Swiper
    wp_enqueue_script('custom-swiper-init', get_stylesheet_directory_uri() . '/js/swiper-init.js', array('swiper-js'), false, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_scripts');


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


    <!-- Swiper -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <!-- Slides -->
            <?php
            $menu_items = new WP_Query(array('post_type' => 'menu_item'));
            if ($menu_items->have_posts()) :
                while ($menu_items->have_posts()) : $menu_items->the_post(); ?>
                    <div class="swiper-slide">
                        <img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
                        <h3><?php the_title(); ?></h3>
                        <!-- Your custom fields like price etc. -->
                    </div>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
        <!-- Add Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Add Arrows -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>


    <section class="splide" aria-label="Splide Basic HTML Example">
  <div class="splide__track">
		<ul class="splide__list">
			<li class="splide__slide">Slide 01</li>
			<li class="splide__slide">Slide 02</li>
			<li class="splide__slide">Slide 03</li>
		</ul>
  </div>
</section>

<section class="splide" aria-label="Splide Basic HTML Example">
    <div class="category-links splide_track">
        <ul class="cat-list splide_list">
            <li><a class="cat-list-item" href="#" data-slug="">All</a></li>

            <?php foreach ($menu_categories as $menu_category) : ?>
                <?php if (empty(!get_term_children($menu_category->term_id, 'menu-categories')) || $menu_category->parent === 0) : ?>

                    <?php
                    $category_image = get_field('category_image', $menu_category);
                    ?>

                    <li class="splide_splide">
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


                                            <p><?php echo $menu_item_description = get_field('menu_item_description'); ?></p>
                                            <!-- description -->
                                        </div>
                                        <?php if ($menu_item_photo) : ?>
                                            <img src="<?php echo $menu_item_photo['url']; ?>" alt="<?php echo $menu_item_photo['alt']; ?>" class="menu-item-photo" width="200" height="auto">
                                        <?php else : ?>
                                            <?php $img_placeholder = wp_get_attachment_url(819); ?>
                                            <img src="<?php echo esc_url($img_placeholder); ?>" alt="Placeholder" class="menu-item-photo" width="200" height="auto">
                                        <?php endif; ?>
                                    </div>
                                <?php
                                }
                                ?>
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
    <svg class="clippy">
        <defs>
            <clippath id="clip-squiggle" clipPathUnits="objectBoundingBox">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.564699 0.25306C0.520873 0.24275 0.451477 0.236619 0.401979 0.278493C0.354393 0.318749 0.355974 0.379902 0.358071 0.405085C0.360335 0.432273 0.366334 0.468951 0.379897 0.508683C0.382791 0.517162 0.386449 0.527034 0.391095 0.537931C0.365324 0.546431 0.340101 0.558181 0.31693 0.574473C0.269553 0.607785 0.232194 0.656686 0.216832 0.721198C0.193319 0.819941 0.234866 0.988207 0.409316 1.10517C0.504306 1.16886 0.591 1.17676 0.643405 1.17442C0.693835 1.17217 0.737074 1.15921 0.759849 1.15239C0.760473 1.1522 0.761083 1.15202 0.761676 1.15184C0.774636 1.14797 0.787153 1.14357 0.799243 1.13873C0.819247 1.14686 0.841116 1.1532 0.864507 1.15631C0.933962 1.16553 0.97498 1.1402 0.992686 1.12668C1.01061 1.11298 1.02293 1.09672 1.02736 1.09088C1.0276 1.09056 1.02782 1.09028 1.02801 1.09002C1.06292 1.04425 1.07759 0.989747 1.0785 0.932724C1.11061 0.920236 1.14021 0.900628 1.16408 0.870851C1.18216 0.848306 1.20357 0.810121 1.20955 0.761837C1.21486 0.718879 1.21159 0.607077 1.08778 0.507815C1.0824 0.503502 1.07682 0.499217 1.07105 0.494997C1.06758 0.479695 1.06353 0.464741 1.05901 0.450343C1.04876 0.417622 1.02812 0.367334 0.993148 0.31702C0.96218 0.27247 0.878962 0.172176 0.750693 0.150051C0.615942 0.126809 0.576121 0.216459 0.566128 0.248283C0.56563 0.24987 0.565154 0.251463 0.564699 0.25306ZM0.494947 0.835202C0.494947 0.835201 0.495024 0.835201 0.495176 0.835209C0.495022 0.835208 0.494946 0.835204 0.494947 0.835202Z" fill="black" />
            </clippath>
        </defs>
    </svg>
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
                <input type="search" class="search-field" id="events-search-input" placeholder="<?php _e('Search events', 'textdomain'); ?>" value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>" name="search" title="<?php _e('Search for:', 'textdomain'); ?>" />
            </label>
            <label for="month-filter">
                <span class="screen-reader-text">
                    <?php _e('Filter by Month:', 'textdomain'); ?>
                </span>
            </label>
            <select name="month" id="month-filter">
                <option value="">Search by Months</option>
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
                        <?php echo date('F j, Y', strtotime(get_field('event_date_time'))); ?>
                    </div>
                    <div class="event_location">
                        <?php the_field('event_location'); ?>
                    </div>

                    <div class="event-link">
                        <a href="<?php the_field('event_link'); ?>" target="_blank">Event Link</a>
                    </div>
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
                    <?php the_field('promotion_description'); ?>
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

<?php
// Enqueue Parent Theme Stylesheet
function storefront_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/assets/css/custom-style.css' );
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Enqueue Fontawesome
function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');

// Include the City Weather Widget
require_once get_stylesheet_directory() . '/assets/widgets/city-weather-widget.php';

// Creation of Custom Post Type Cities
function create_cities_post_type() {
    $labels = array(
        'name'               => __('Cities'),
        'singular_name'      => __('City'),
        'menu_name'          => __('Cities'),
        'name_admin_bar'     => __('City'),
        'add_new'            => __('Add New'),
        'add_new_item'       => __('Add New City'),
        'edit_item'          => __('Edit City'),
        'new_item'           => __('New City'),
        'view_item'          => __('View City'),
        'all_items'          => __('All Cities'),
        'search_items'       => __('Search Cities'),
        'not_found'          => __('No cities found'),
        'not_found_in_trash' => __('No cities found in Trash')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'cities'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-location-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'custom-fields'),
    );

    register_post_type('cities', $args);
}
add_action('init', 'create_cities_post_type');

// Adding of Meta Boxes
function add_city_meta_boxes() {
    add_meta_box(
        'city_coordinates',
        __('City Coordinates'),
        'render_city_meta_box',
        'cities',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

function render_city_meta_box($post) {
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);

    ?>
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <th style="text-align: left; padding: 10px;">Longitude</th>
            <td style="padding: 10px;">
                <input type="text" name="longitude" value="<?php echo esc_attr($longitude); ?>" style="width:100%;" />
            </td>
        </tr>
        <tr>
            <th style="text-align: left; padding: 10px;">Latitude</th>
            <td style="padding: 10px;">
                <input type="text" name="latitude" value="<?php echo esc_attr($latitude); ?>" style="width:100%;" />
            </td>
        </tr>
    </table>
    <?php
}

function save_city_meta_box_data($post_id) {
    if (array_key_exists('latitude', $_POST)) {
        update_post_meta($post_id, 'latitude', sanitize_text_field($_POST['latitude']));
    }
    if (array_key_exists('longitude', $_POST)) {
        update_post_meta($post_id, 'longitude', sanitize_text_field($_POST['longitude']));
    }
}
add_action('save_post', 'save_city_meta_box_data');

// Creation of Taxonomy Countries
function create_countries_taxonomy() {
    $labels = array(
        'name'              => _x('Countries', 'taxonomy general name'),
        'singular_name'     => _x('Country', 'taxonomy singular name'),
        'search_items'      => __('Search Countries'),
        'all_items'         => __('All Countries'),
        'edit_item'         => __('Edit Country'),
        'update_item'       => __('Update Country'),
        'add_new_item'      => __('Add New Country'),
        'new_item_name'     => __('New Country Name'),
        'menu_name'         => __('Countries'),
    );

    register_taxonomy('countries', array('cities'), array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'countries'),
    ));
}
add_action('init', 'create_countries_taxonomy');

// Add custom action hooks before and after
function before_city_table_hook() {
    echo '<label id="hook-label">Data Will Be Loaded Below</label>';
}
add_action('before_city_table', 'before_city_table_hook');

function after_city_table_hook() {
    echo '<label id="hook-label">Data Loading Completed</label>';
}
add_action('after_city_table', 'after_city_table_hook');

// Enqueue Scripts for Ajax
function city_list_enqueue_scripts() {
    wp_enqueue_script('city-ajax-script', get_stylesheet_directory_uri() . '/assets/js/city-list.js', array('jquery'), null, true);
    wp_localize_script('city-ajax-script', 'city_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'city_list_enqueue_scripts');

// Handle Ajax Request
function fetch_city_data() {
    global $wpdb;

    $search_term = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $page_number = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $items_per_page = isset($_POST['items_per_page']) ? intval($_POST['items_per_page']) : 10;

    $offset = ($page_number - 1) * $items_per_page;

    // Query to count the total number of results (for pagination purposes)
    $total_query = "
        SELECT COUNT(*)
        FROM $wpdb->posts p
        INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
        INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN $wpdb->terms tm ON tt.term_id = tm.term_id
        INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'latitude'
        INNER JOIN $wpdb->postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'longitude'
        WHERE p.post_type = 'cities' AND p.post_status = 'publish'";

    if ($search_term) {
        $total_query .= $wpdb->prepare(" AND p.post_title LIKE %s", '%' . $search_term . '%');
    }

    $total_results = $wpdb->get_var($total_query);

    // Main query with pagination
    $query = "
        SELECT p.ID, p.post_title, tm.name AS country, pm.meta_value AS latitude, pm2.meta_value AS longitude
        FROM $wpdb->posts p
        INNER JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
        INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN $wpdb->terms tm ON tt.term_id = tm.term_id
        INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'latitude'
        INNER JOIN $wpdb->postmeta pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'longitude'
        WHERE p.post_type = 'cities' AND p.post_status = 'publish'";

    if ($search_term) {
        $query .= $wpdb->prepare(" AND p.post_title LIKE %s", '%' . $search_term . '%');
    }

    $query .= " LIMIT $offset, $items_per_page";

    $results = $wpdb->get_results($query);

    $cities_html = '';
    if ($results) {
        foreach ($results as $city) {
            $api_key = 'a50bfd9d9a339878b18e8ad4ad69d97a';  // Replace with your OpenWeatherMap API key
            $api_url = "http://api.openweathermap.org/data/2.5/weather?lat={$city->latitude}&lon={$city->longitude}&appid={$api_key}&units=metric";
            $weather_data = wp_remote_get($api_url);
            if (is_array($weather_data) && !is_wp_error($weather_data)) {
                $weather_body = json_decode($weather_data['body']);
                $temperature = $weather_body->main->temp;

                $cities_html .= "<tr>
                        <td>{$city->country}</td>
                        <td>{$city->post_title}</td>
                        <td>{$temperature}</td>
                      </tr>";
            }
        }
    } else {
        $cities_html .= '<tr><td colspan="3">No cities found.</td></tr>';
    }

    $response = array(
        'html' => $cities_html,
        'total_results' => $total_results,
        'current_page' => $page_number,
        'items_per_page' => $items_per_page
    );

    wp_send_json($response);

    wp_die(); // This is required to terminate the AJAX request
}
add_action('wp_ajax_nopriv_fetch_city_data', 'fetch_city_data');
add_action('wp_ajax_fetch_city_data', 'fetch_city_data');
?>
<?php
class City_Weather_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('city_weather_widget', __('City Weather'), array('description' => __('Displays the city name and current temperature')));
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        if ($city_id) {
            $latitude = get_post_meta($city_id, 'latitude', true);
            $longitude = get_post_meta($city_id, 'longitude', true);
            $city_name = get_the_title($city_id);

            $api_key = 'a50bfd9d9a339878b18e8ad4ad69d97a';
            $api_url = "http://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&appid={$api_key}&units=metric";
            $weather_data = wp_remote_get($api_url);

            if (is_array($weather_data) && !is_wp_error($weather_data)) {
                $weather_body = json_decode($weather_data['body']);
                $temperature = $weather_body->main->temp ?? __('N/A');
                $weather = $weather_body->weather[0]->main ?? __('N/A');
                echo '<h3>' . esc_html($city_name) . '</h3><p>' . __('Temperature: ') . esc_html($temperature) . ' °C</p><p>' . __('Weather: ') . esc_html($weather) . '</p>';
            }
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $city_id = !empty($instance['city_id']) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('city_id'); ?>"><?php _e('City:'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('city_id'); ?>" name="<?php echo $this->get_field_name('city_id'); ?>">
                <?php
                $cities = get_posts(array('post_type' => 'cities', 'numberposts' => -1, 'fields' => 'ids'));
                foreach ($cities as $city_id) {
                    echo '<option value="' . esc_attr($city_id) . '">' . esc_html(get_the_title($city_id)) . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['city_id'] = strip_tags($new_instance['city_id']);
        return $instance;
    }
}

function register_city_weather_widget() {
    register_widget('City_Weather_Widget');
}
add_action('widgets_init', 'register_city_weather_widget');
?>
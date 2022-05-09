<?php

/** Enable post thumbnails */
function wl_post_thumbnails(){
    add_theme_support( 'post-thumbnails');
}
add_action( 'after_setup_theme', 'wl_post_thumbnails' );

/** Connection styles */
function wl_enqueue_style() {
    wp_enqueue_style( 'style', get_template_directory_uri(). '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'wl_enqueue_style' );

/** Registration custom taxonomies */
function create_custom_taxonomy(){
    register_taxonomy( 'brands', [ 'car' ], [
        'label'                 => 'Марки',
        'labels'                => [
            'name'              => 'Марка',
            'singular_name'     => 'Марка',
        ],
        'public'                => true,
        'hierarchical'          => true,
    ]);
    register_taxonomy( 'countries', [ 'car' ], [
        'label'                 => 'Страна производитель',
        'labels'                => [
            'name'              => 'Страна производитель',
            'singular_name'     => 'Страна производитель',
        ],
        'public'                => true,
        'hierarchical'          => true,
    ]);
}
add_action( 'init', 'create_custom_taxonomy' );

/** Add custom post type */
function register_car_types(){
    register_post_type( 'car', [
        'label'  => 'Cars',
        'labels' => [
            'name'               => 'Машины',
            'singular_name'      => 'Машина',
        ],
        'public'              => true,
        'show_in_menu'        => true,
        'menu_position'       => 4,
        'hierarchical'        => false,
        'supports'            => [ 'title', 'editor', 'thumbnail'],
        'taxonomies'          => ['brands', 'countries'],
        'rewrite'             => true,
    ] );
}
add_action( 'init', 'register_car_types' );

/** Create custom fields */
function custom_fields() {
    add_meta_box( 'color_picker_fields', 'Цвет', 'color_picker_fields_func', 'car', 'normal', 'high');
    add_meta_box( 'fuel_fields', 'Топливо', 'fuel_fields_func', 'car', 'normal', 'high');
    add_meta_box( 'power_fields', 'Мощность', 'power_fields_func', 'car', 'normal', 'high');
    add_meta_box( 'price_fields', 'Цена', 'price_fields_func', 'car', 'normal', 'high');
}
add_action('add_meta_boxes', 'custom_fields', 1);

/** View of color picker */
function color_picker_fields_func($cars){ ?>
    <input type="color" name="extra[color]" value="<?= get_post_meta($cars->ID, 'color', 1); ?>">
    <input type="hidden" name="color_fields_value" value="<?= wp_create_nonce(__FILE__); ?>" />
<?php }

/** View of fuel fields */
function fuel_fields_func($cars){ ?>
    <select name="extra[select]">
        <?php $sel_v = get_post_meta($cars->ID, 'select', 1); ?>
        <option value="0">----</option>
        <option value="1" <?php selected( $sel_v, '1' )?>>АИ-80</option>
        <option value="2" <?php selected( $sel_v, '2' )?>>АИ-95</option>
        <option value="3" <?php selected( $sel_v, '3' )?>>АИ-98</option>
    </select>
    <input type="hidden" name="fuel_fields_value" value="<?= wp_create_nonce(__FILE__); ?>" />
<?php
}

/** View of power fields */
function power_fields_func($cars){ ?>
    <input type="number" id="power" name="extra[power]" min="10" max="200" value="<?= get_post_meta($cars->ID, 'power', 1); ?>">
    <input type="hidden" name="power_fields_value" value="<?= wp_create_nonce(__FILE__); ?>" />
<?php }

/** View of price fields */
function price_fields_func($cars){ ?>
    <input type="number" id="price" name="extra[price]" value="<?= get_post_meta($cars->ID, 'price', 1); ?>">
    <input type="hidden" name="price_fields_value" value="<?= wp_create_nonce(__FILE__); ?>" />
<?php }

/** Save date in custom fields */
function custom_fields_update($cars_id){
    if (
        empty( $_POST['extra'] )
        || ! wp_verify_nonce( $_POST['fuel_fields_value'], __FILE__ )
        || ! wp_verify_nonce( $_POST['color_fields_value'], __FILE__ )
        || ! wp_verify_nonce( $_POST['power_fields_value'], __FILE__ )
        || ! wp_verify_nonce( $_POST['price_fields_value'], __FILE__ )
        || wp_is_post_autosave( $cars_id )
        || wp_is_post_revision( $cars_id )
    )
        return false;

    /* Save date */
    $_POST['extra'] = array_map( 'sanitize_text_field', $_POST['extra'] );
    foreach( $_POST['extra'] as $key => $value ){
        if( empty($value) ){
            delete_post_meta( $cars_id, $key ); // delete field if value is empty
            continue;
        }
        update_post_meta( $cars_id, $key, $value );
    }

    return $cars_id;
}
/** Enable update on save */
add_action( 'save_post', 'custom_fields_update', 0 );

/** Add new field in customizer */
add_action('customize_register', function($customizer){
    $customizer->add_section(
        'phone_number',
        array(
            'title' => 'Номер телефона',
            'description' => 'Добавьте номер телефона',
            'priority' => 11,
        )
    );
    $customizer->add_setting(
        'custom_phone_number',
        array('default' => '')
    );
    $customizer->add_control(
        'custom_phone_number',
        array(
            'label' => 'Номер телефона',
            'section' => 'phone_number',
            'type' => 'text',
        )
    );

    $customizer->add_section(
        'site_logo',
        array(
            'title' => 'Логотип сайта',
            'description' => 'Загрузите логотип',
            'priority' => 12,
        )
    );
    $customizer->add_setting('custom_site_logo');
    $customizer->add_control(
        new WP_Customize_Image_Control(
            $customizer,
            'img-upload',
            array(
                'label' => 'Загрузка изображения',
                'section' => 'site_logo',
                'settings' => 'custom_site_logo'
            )
        )
    );
});

/** Add shortcode */
function get_list_cars(){
    $cars = get_posts([
        'numberposts' => 10,
        'category'    => 0,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'post_type'   => 'car',
    ]);

    echo '<br> Список автомобилей: ';
    echo '<ul>';
    foreach ($cars as $car) {
        echo '<a href="'.get_the_permalink($car->ID).'">';
        echo '<li>';
        echo $car->post_title;
        echo '</li>';
        echo '</a>';
    }
    echo '</ul>';
}
add_shortcode( 'list_cars', 'get_list_cars' );

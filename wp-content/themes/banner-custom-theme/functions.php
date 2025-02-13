<?php
// Habilitar menús en WordPress
function custom_theme_setup()
{
    register_nav_menus([
        'main-menu' => 'Main Menu'
    ]);
}
add_action('init', function () {
    if (current_user_can('administrator')) {
        delete_transient('all');
    }
});

add_action('after_setup_theme', 'custom_theme_setup');

// Encolar estilos
function custom_theme_styles()
{
    wp_enqueue_style('main-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'custom_theme_styles');


function add_parent_menu_class($classes, $item)
{
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'menu-root';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_parent_menu_class', 10, 2);


function get_dynamic_banner_image()
{
    $theme_path = get_template_directory_uri() . '/assets/images/';
    $default_image = $theme_path . '47504.jpg';

    $current_id = get_queried_object_id();
    $menu_locations = get_nav_menu_locations();

    if ($current_id == 0) {
        return $default_image;
    }

    if (!isset($menu_locations['main-menu'])) {
        return $default_image;
    }

    $menu_id = $menu_locations['main-menu'];
    $menu_items = wp_get_nav_menu_items($menu_id);

    foreach ($menu_items as $item) {
        if ($current_id == $item->object_id) {

            $root_item = ($item->menu_item_parent == 0)
                ? $item
                : get_root_parent($item, $menu_items);

            if ($root_item && $root_item->title === 'Root A') {
                return $theme_path . '18697.jpg';
            } elseif ($root_item && $root_item->title === 'Root B') {
                return $theme_path . '18707.jpg';
            }
        }
    }
    return $default_image; // Si no encuentra coincidencias, usa la imagen por defecto
}


function get_root_parent($item, $menu_items)
{
    $parent_id = $item->menu_item_parent;

    while ($parent_id != 0) {
        foreach ($menu_items as $parent_item) {
            if ($parent_item->ID == $parent_id) {
                $item = $parent_item;
                $parent_id = $parent_item->menu_item_parent;
                break;
            }
        }
    }
    /* var_dump($item);
    die(); */
    return $item;
}

function create_custom_post_product()
{
    register_post_type(
        'product',
        array(
            'labels' => array(
                'name' => __('Products'),
                'singular_name' => __('Product')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'products'),
            'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        )
    );
}
add_action('init', 'create_custom_post_product');




add_action('phpmailer_init', function ($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = 'cb9043c0375dc1';
    $phpmailer->Password = '06ede68e375c6e';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->setFrom('no-reply@localhost.com', 'Local Test');
});



// Looking to send emails in production? Check out our Email API/SMTP product!

/* add_action('init', function () {
    if (isset($_GET['send_test_email'])) {
        $to      = 'test@mailtrap.io';
        $subject = 'Test Email';
        $message = 'This is a test email.';

        $headers = ['From: Local Test <no-reply@localhost.com>'];

        $mail_sent = wp_mail($to, $subject, $message, $headers);

        if ($mail_sent) {
            echo '✅ Test email sent!';
        } else {
            echo '❌ Test email failed.';
        }
        exit;
    }
}); */


/* add_action('wp_mail_failed', function ($wp_error) {
    echo '<pre>';
    print_r($wp_error);
    echo '</pre>';
}); */


add_action('init', function () {
    if (isset($_GET['test_weekly_report'])) {
        pod_send_weekly_report();
        exit;
    }
});

<?php
/*
Plugin Name: Product of the Day
Description: A WordPress plugin to manage and display "Products of the Day". shortcode=  [product_of_the_day]
Version: 1.0
Author: Fabio Santos
License: GPL2
Text Domain: product-of-the-day
*/

if (!defined('ABSPATH')) exit; // Evitar acceso directo

include_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
include_once plugin_dir_path(__FILE__) . 'includes/product-functions.php';
include_once plugin_dir_path(__FILE__) . 'includes/cron-job.php';

function pod_activate()
{
    global $wpdb;

    $products_table = $wpdb->prefix . 'pod_products';
    $clicks_table = $wpdb->prefix . 'pod_clicks';

    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Verificar y crear la tabla pod_products si no existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$products_table'") != $products_table) {
        $sql_products = "CREATE TABLE $products_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            summary TEXT NOT NULL,
            image_url VARCHAR(255),
            is_product_of_the_day BOOLEAN DEFAULT 0
        ) $charset_collate;";
        dbDelta($sql_products);
    }

    // Verificar y crear la tabla pod_clicks si no existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$clicks_table'") != $clicks_table) {
        $sql_clicks = "CREATE TABLE $clicks_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT,
            click_date DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";
        dbDelta($sql_clicks);
    }

    // Verificar y crear la tabla pod_product_settings si no existe

}
register_activation_hook(__FILE__, 'pod_activate');




function pod_display_product_carousel()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'pod_products';
    $number_of_products = get_option('number_products', 1);

    $products = $wpdb->get_results("SELECT * FROM $table_name WHERE is_product_of_the_day = 1 ORDER BY RAND() LIMIT $number_of_products");

    if (!$products) {
        return "<p>There are no featured products today.</p>";
    }
    ob_start(); ?>

    <div class="container  mt-5">
        <h3 class="text-center text-uppercase mb-5">
            <?php echo get_option('block_title', 'Products'); ?>
        </h3>

        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($products as $product) : ?>
                    <div class="swiper-slide">
                        <div class="pod-product">
                            <img style="max-width: 100%; border-radius: 10px;" src="<?php echo esc_url($product->image_url); ?>" alt="<?php echo esc_attr($product->name); ?>">
                            <h3><?php echo esc_html($product->name); ?></h3>
                            <p><?php echo esc_html(mb_strimwidth($product->summary, 0, 150, '...')); ?></p>
                            <a href="<?php echo home_url('product-of-the-day/' . $product->id); ?>" class='cta-button' data-product-id='<?php echo $product->id; ?>'>Read more</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php return ob_get_clean();
}
add_shortcode('product_of_the_day', 'pod_display_product_carousel');





function pod_enqueue_scripts()
{
    wp_enqueue_style('bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css', [], null);
    wp_enqueue_script('bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js', ['jquery'], null, true);
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], null);
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], null, true);
    wp_enqueue_script('pod-carousel', plugin_dir_url(__FILE__) . 'carousel.js', ['swiper-js'], null, true);
}
add_action('wp_enqueue_scripts', 'pod_enqueue_scripts');

function pod_custom_styles()
{
    echo '
    <style>


.swiper-container {
        width: 100%;
        max-width: 100%;
        overflow: hidden;
        padding: 20px 5px; 
    }
    
  
        .pod-carousel {
            width: 100%;
            padding: 20px 0;
        }
        .swiper-slide {
            background: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-of-the-day img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .product-of-the-day h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .product-of-the-day p {
            font-size: 14px;
            color: #666;
        }
        .cta-button {
            display: inline-block;
            padding: 8px 15px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .swiper-button-prev, .swiper-button-next {
            color: #0073aa;
        }
        .swiper-pagination-bullet-active {
            background: #0073aa;
        }
        .container-product{
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            padding: 35px;
        }
        .title-product{
            box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px, rgba(0, 0, 0, 0.3) 0px 30px 60px -30px, rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
            border-radius: 20px;
            padding: 6px 0px;
        }
    </style>
    ';
}
add_action('wp_head', 'pod_custom_styles');




/* URL Rewrite para la vista del producto */
function pod_add_rewrite_rules()
{
    add_rewrite_rule('^product-of-the-day/([0-9]+)/?$', 'index.php?pod_product_id=$matches[1]', 'top');
}
add_action('init', 'pod_add_rewrite_rules');

/* Registrar la variable de query */
function pod_add_query_vars($vars)
{
    $vars[] = 'pod_product_id';
    return $vars;
}
add_filter('query_vars', 'pod_add_query_vars');

/* Cargar la plantilla personalizada */
function pod_template_redirect()
{
    $product_id = get_query_var('pod_product_id');
    if ($product_id) {
        pod_display_product_detail($product_id);
        exit;
    }
}
add_action('template_redirect', 'pod_template_redirect');

/* Función para mostrar el detalle del producto */
function pod_display_product_detail($product_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'pod_clicks';
    

    $wpdb->insert($table_name, [
        'product_id' => $product_id,
        'click_date' => current_time('mysql')
    ]);

    

    
    global $wpdb;
    $table_name = $wpdb->prefix . 'pod_products';

    $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $product_id));

    if (!$product) {
        wp_die('Product not found');
    }

    get_header();
?>
    <div class="container mt-5">
        <div class="row  container-product">



            <h2 class="mb-5 text-center text-uppercase title-product"><?php echo esc_html($product->name); ?></h2>

            <div class="col-md-6">

                <img src="<?php echo esc_url($product->image_url); ?>" alt="<?php echo esc_attr($product->name); ?>" style="max-width: 100%; border-radius: 10px;">
            </div>
            <div class="col-md-6 ">

                <p><?php echo esc_html($product->summary); ?></p>
            </div>
        </div>

    </div>
<?php

}

/* Refrescar las reglas de reescritura al activar el plugin */
function pod_flush_rewrite_rules()
{
    pod_add_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'pod_flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

add_action('wp_ajax_upload_product_image', function() {
    if (!function_exists('wp_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $upload_overrides = ['test_form' => false];
    $file = $_FILES['image_file'];

    $movefile = wp_handle_upload($file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        wp_send_json_success(['url' => $movefile['url']]);
    } else {
        wp_send_json_error(['message' => $movefile['error']]);
    }
});

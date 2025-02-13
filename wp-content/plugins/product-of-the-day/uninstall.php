<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

$products_table = $wpdb->prefix . 'pod_products';
$clicks_table = $wpdb->prefix . 'pod_clicks';

// Eliminar la tabla de productos si existe
if ($wpdb->get_var("SHOW TABLES LIKE '$products_table'") == $products_table) {
    $wpdb->query("DROP TABLE IF EXISTS $products_table");
}

// Eliminar la tabla de clics si existe
if ($wpdb->get_var("SHOW TABLES LIKE '$clicks_table'") == $clicks_table) {
    $wpdb->query("DROP TABLE IF EXISTS $clicks_table");
}

// Eliminar opciones de configuración del plugin
delete_option('pod_settings');
delete_option('notification_email');


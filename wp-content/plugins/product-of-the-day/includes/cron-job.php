<?php
// Programar el evento al iniciar WordPress
add_action('wp', function () {
    if (!wp_next_scheduled('pod_weekly_report_event')) {
        wp_schedule_event(strtotime('next Monday 02:00'), 'weekly', 'pod_weekly_report_event');
    }
});

// Acción que dispara el envío del informe
add_action('pod_weekly_report_event', 'pod_send_weekly_report');

function pod_send_weekly_report()
{
    global $wpdb;
    $products_table = $wpdb->prefix . 'pod_products';
    $clicks_table = $wpdb->prefix . 'pod_clicks';

    $products = $wpdb->get_results("SELECT * FROM $products_table WHERE is_product_of_the_day = 1");

    $report = "Weekly Featured Products:\n\n";
    foreach ($products as $product) {
        $clicks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $clicks_table WHERE product_id = %d", $product->id));
        $report .= "{$product->name} - Clicks: {$clicks}\n";
    }

    $to = get_option('notification_email', 'default@mailtrap.io');
    $subject = 'Weekly Product Report';
    $message = $report;

    $headers = ['From: Local Test <no-reply@localhost.com>'];

    $mail_sent = wp_mail($to, $subject, $message, $headers);

    if ($mail_sent) {
        echo ('✅ Mail sent successfully to ' . $to);
    } else {
        $error = error_get_last();
        echo ('❌ Failed to send mail to ' . $to . ' | Error: ' . print_r($error, true));
    }
}

<?php function enqueue_dropzone_scripts()
{
    wp_enqueue_style('dropzone-css', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css');
    wp_enqueue_script('dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js', [], null, true);

    // Incluir script personalizado
    wp_enqueue_script('custom-dropzone', plugin_dir_url(__FILE__) . 'js/custom-dropzone.js', ['dropzone-js'], null, true);

    // Pasar la URL de admin-ajax.php a JavaScript
    wp_localize_script('custom-dropzone', 'dropzone_ajax', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
}
add_action('admin_enqueue_scripts', 'enqueue_dropzone_scripts');

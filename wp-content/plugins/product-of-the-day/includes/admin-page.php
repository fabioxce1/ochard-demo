<?php
add_action('admin_menu', function () {
    add_menu_page('Product of the Day', 'Product of the Day', 'manage_options', 'pod-products', 'pod_admin_page');
});

function pod_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'pod_products';

    // Guardar o actualizar producto
    if (isset($_POST['submit_product'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pod_products';
        $upload_dir = WP_PLUGIN_DIR . '/product-of-the-day/assets/images/'; // Carpeta de imágenes

        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $name = sanitize_text_field($_POST['name']);
        $summary = sanitize_textarea_field($_POST['summary']);
        $is_product_of_the_day = isset($_POST['is_product_of_the_day']) ? 1 : 0;


        $old_image_url = '';
        if ($product_id > 0) {
            $old_image_url = $wpdb->get_var($wpdb->prepare("SELECT image_url FROM $table_name WHERE id = %d", $product_id));
        }


        if (!empty($_FILES['image_file']['name'])) {
            $file = $_FILES['image_file'];
            $file_name = time() . '_' . sanitize_file_name($file['name']); // Nombre único
            $file_path = $upload_dir . $file_name;
            $image_url = plugins_url('product-of-the-day/assets/images/' . $file_name);


            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $image_url = esc_url_raw($image_url);


                if (!empty($old_image_url)) {
                    $old_image_path = str_replace(plugins_url('product-of-the-day/assets/images/'), $upload_dir, $old_image_url);
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
            } else {
                echo "<div class='notice notice-error'><p>Error uploading image.</p></div>";
                return;
            }
        } else {

            $image_url = $old_image_url;
        }


        if ($is_product_of_the_day) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE is_product_of_the_day = 1 AND id != %d",
                $product_id
            ));

            if ($count >= 5) {
                echo "<div class='notice notice-error'><p>You can only have 5 featured products.</p></div>";
                return;
            }
        }

        if ($product_id > 0) {
            $wpdb->update(
                $table_name,
                [
                    'name' => $name,
                    'summary' => $summary,
                    'image_url' => $image_url,
                    'is_product_of_the_day' => $is_product_of_the_day
                ],
                ['id' => $product_id]
            );
            echo "<div class='notice notice-success'><p>Product updated successfully.</p></div>";
        } else {
            $wpdb->insert(
                $table_name,
                [
                    'name' => $name,
                    'summary' => $summary,
                    'image_url' => $image_url,
                    'is_product_of_the_day' => $is_product_of_the_day
                ]
            );
            echo "<div class='notice notice-success'><p>Product saved successfully.</p></div>";
        }
    }



    if (isset($_POST['save_settings'])) {
        $block_title = sanitize_text_field($_POST['block_title']);
        $notification_email = sanitize_email($_POST['notification_email']);
        $number_products = $_POST['number_products'];

        update_option('block_title', $block_title);
        update_option('notification_email', $notification_email);
        update_option('number_products', $number_products);

        echo '<div class="notice notice-success is-dismissible"><p>Configuración guardada correctamente.</p></div>';
    }

    if (isset($_GET['delete_product'])) {
        $product_id = intval($_GET['delete_product']);

        $image_url = $wpdb->get_var($wpdb->prepare("SELECT image_url FROM $table_name WHERE id = %d", $product_id));

        if (!empty($image_url)) {
            $upload_dir = WP_PLUGIN_DIR . '/product-of-the-day/assets/images/';
            $image_path = str_replace(plugins_url('product-of-the-day/assets/images/'), $upload_dir, $image_url);

            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $wpdb->delete($table_name, ['id' => $product_id]);

        echo "<div class='notice notice-success'><p>Product removed successfully.</p></div>";
    }



    $edit_product = null;
    if (isset($_GET['edit_product'])) {
        $edit_id = intval($_GET['edit_product']);
        $edit_product = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $edit_id");
    }

    $products = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
?>

    <style>
        .container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .form-box {
            width: 48%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="email"],
        input[type="url"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #0073aa;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #005a87;
        }
    </style>

    <h1>Product Management</h1>

    <div class="container">

        <form method="POST" action="" class="form-box">
            <h2>Settings</h2>
            <label for="">Title for block </label>
            <input type="text" name="block_title" placeholder="Title for block (example: Products of the Day)"
                value="<?php echo get_option('block_title', 'Products of the Day'); ?>" required><br>

            <label for="">Email for weekly report </label>
            <input type="email" name="notification_email" placeholder="Email for weekly report"
                value="<?php echo get_option('notification_email', ''); ?>" required><br>

            <label for="">Number of products to display (max 5)</label>
            <input type="number" name="number_products" placeholder="Number of products to display  (max 5)" max="5" min="1"
                value="<?php echo get_option('number_products', ''); ?>" required><br>

            <button type="submit" name="save_settings" class="button button-primary">Save configuration</button>
        </form>


        <form method="POST" class="form-box" enctype="multipart/form-data">
            <h2><?php echo $edit_product ? 'Edit Product' : 'Create Product'; ?></h2>
            <input type="hidden" name="product_id" value="<?php echo $edit_product ? esc_attr($edit_product->id) : ''; ?>">

            <label for="">Name </label>
            <input type="text" name="name" placeholder="Name"
                value="<?php echo $edit_product ? esc_attr($edit_product->name) : ''; ?>" required><br>

            <label for="">Summary</label>
            <textarea name="summary" placeholder="Summary" required><?php echo $edit_product ? esc_textarea($edit_product->summary) : ''; ?></textarea><br>


            <input type="file" name="image_file" accept="image/*"><br>

            <br>

            <?php if ($edit_product && !empty($edit_product->image_url)): ?>
                <img src="<?php echo esc_url($edit_product->image_url); ?>" alt="Product Image" width="100"><br>
            <?php endif; ?>

            <label>
                <input type="checkbox" name="is_product_of_the_day" <?php echo $edit_product && $edit_product->is_product_of_the_day ? 'checked' : ''; ?>>
                ¿Product of the Day?
            </label><br><br>

            <button type="submit" name="submit_product" class="button button-primary">
                <?php echo $edit_product ? 'Update Product' : 'Save Product'; ?>
            </button>
        </form>
    </div>




    <h2><?php echo isset($block_title) ? esc_html($block_title) : 'Product list'; ?></h2>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Summary</th>
                <th>Product of the Day</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo esc_html($product->id); ?></td>
                    <td><?php echo esc_html($product->name); ?></td>
                    <td>
                        <?php if (!empty($product->image_url)): ?>
                            <img src="<?php echo esc_url($product->image_url); ?>" style="max-width: 50px; height: auto;">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($product->summary); ?></td>
                    <td><?php echo $product->is_product_of_the_day ? '✅ Yes' : '❌ No'; ?></td>
                    <td>
                        <a href="?page=pod-products&edit_product=<?php echo esc_attr($product->id); ?>" class="button">Edit</a>
                        <a href="?page=pod-products&delete_product=<?php echo esc_attr($product->id); ?>" class="button button-danger" onclick="return confirm('Are you sure to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



<?php
}

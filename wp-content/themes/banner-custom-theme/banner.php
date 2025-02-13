<?php
$image_url = get_dynamic_banner_image();
?>
<div class="dynamic-banner" style="background-image: url('<?php echo esc_url($image_url); ?>'); ">
    <h1 style="color:white"><?php the_title(); ?></h1>
</div>
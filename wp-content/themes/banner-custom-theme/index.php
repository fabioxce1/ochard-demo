<?php get_header(); ?>

<main>

    <?php get_template_part('banner'); ?>


    <?php
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
    <?php if (is_front_page() || is_home()) : ?>

        <section class="productos-del-dia">
            <?php echo do_shortcode('[product_of_the_day]'); ?>
        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
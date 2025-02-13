<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo wp_get_document_title(); ?></title>

    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <style type="text/css" media="screen">
        .wp-block-site-title :where(a) {
            color: inherit;
            font-family: inherit;
            font-size: inherit;
            font-style: inherit;
            letter-spacing: inherit;
            line-height: inherit;
            text-decoration: inherit;
            font-weight: 700 !important;
        }
    </style>
    <?php if (is_singular()) wp_enqueue_script('comment-reply'); ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- Enlace para saltar al contenido principal (accesibilidad) -->
    <a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e('Saltar al contenido', 'tu-tema'); ?></a>
    <div class="container">

        <header class="site-header wp-block-template-part">
            <div class="wp-block-group alignfull is-layout-flow wp-block-group-is-layout-flow">
                <div class="wp-block-group has-global-padding is-layout-constrained wp-block-group-is-layout-constrained">
                    <div class="wp-block-group alignwide is-content-justification-space-between is-nowrap is-layout-flex wp-block-group-is-layout-flex"
                        style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30); justify-content: space-between;">

                        <!-- Título del sitio -->
                        <p class="wp-block-site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </p>

                        <!-- Navegación principal -->
                        <nav class="main-nav" aria-label="<?php esc_attr_e('Menú principal', 'tu-tema'); ?>">
                            <?php
                            wp_nav_menu(array(
                                'theme_location'  => 'main-menu',
                                'menu_class'      => 'main-menu',
                                'container'       => false, // Evitar contenedores innecesarios
                                'fallback_cb'     => function () {
                                    echo '<ul class="main-menu"><li><a href="#">' . esc_html__('Configura tu menú aquí', 'tu-tema') . '</a></li></ul>';
                                }
                            ));
                            ?>
                        </nav>

                    </div>
                </div>
            </div>
        </header>
    </div>

    <main id="main-content" role="main">
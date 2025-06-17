<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php bloginfo('name'); ?> | <?php the_title(); ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo home_url('/favicon.ico'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
    <div class="site-header__title"><?php bloginfo('name'); ?></div>
</header>
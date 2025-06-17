<?php get_header(); ?>
<main class="content">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h1 class="content__title"><?php the_title(); ?></h1>
        <div class="content__body"><?php the_content(); ?></div>
    <?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>
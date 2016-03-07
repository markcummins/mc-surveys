<?php get_header(); ?>
    <div>

        <?php while ( have_posts() ) : the_post(); ?>
            <?php the_post_thumbnail(); ?>
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php the_content(); ?>

            <?php do_action('mc_get_survey', get_the_id()); ?>
        <?php endwhile; ?>
        
	</div>
<?php get_footer(); ?>

<?php get_header(); ?>
<div class="container">
<div class="row">
<br/>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <div id="post-<?php the_ID(); ?>">
        <div class="col-sm-3">
            <?php the_post_thumbnail(); ?>
        </div>
        <div class="col-sm-9">
            <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php the_content(); ?>
        </div>
        </div>
    <?php endwhile; ?>
<?php get_template_part('pagination'); ?>
<?php else: ?>
    <div class="col-sm-12">
        <h3>No surveys available</h3>
        <p>There are currently no surveys available, check back soon for new content.</p>7
    </div>
<?php endif; ?>
</div>
</div>
<?php get_footer(); ?>
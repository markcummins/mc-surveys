<?php get_header(); ?>
    <div>
        <?php while ( have_posts() ) : the_post(); ?>
           
            <div class="mc-survey-single">             
                <header>
                    <br/>
                    <div class="mc-survey-single-thumb">
                        <?php if ( has_post_thumbnail() ): ?>
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                        <?php else: ?>
                            <?php do_action('mc_get_default_thumbnail'); ?>                                
                        <?php endif; ?>
                    </div><div class="mc-survey-single-meta">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>
                </header>
                <article class="hentry">
                        <?php do_action('mc_get_survey', get_the_id()); ?>
                </article>
            </div>
            
        <?php endwhile; ?>        
	</div>
<?php get_footer(); ?>
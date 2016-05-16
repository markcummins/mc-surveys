<?php get_header(); ?>
    <div>
        <?php while ( have_posts() ) : the_post(); ?>
           
            <div class="row">                
                <header class="entry-header">
                    <br/>
                    <div class="col-sm-4 col-md-3">
                        <?php
                        if ( has_post_thumbnail() )
                            the_post_thumbnail( 'medium', array('class' => "img-responsive"));
                        ?>
                    </div>
                    <div class="col-sm-8 col-md-9">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>
                </header>
                <article class="hentry">
                    <div class="col-xs-12">
                        <br/>
                        <?php do_action('mc_get_survey', get_the_id()); ?>
                    </div>
                </article>
            </div>
            
        <?php endwhile; ?>        
	</div>
<?php get_footer(); ?>

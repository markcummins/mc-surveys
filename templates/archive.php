<?php get_header(); ?>

        <div class="row">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>">
                    <div class="col-sm-2 hidden-xs loop-thumbnail">
                        <?php
                        if ( has_post_thumbnail() )
                            the_post_thumbnail( 'small', array('class' => "img-responsive"));
                        ?>
                    </div>
                    <div class="col-sm-10">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </h3>
                        <?php the_excerpt(); ?>

                        <i class="fa fa-fw fa-clock-o"></i>
                        <?php the_time('F j, Y'); ?>
                    </div>
                </article>
                <div class="clearfix"></div>

			
			<?php endwhile;

		else :
			
            echo "<h3>". __("No surveys available", 'mc_survey') ."</h3>";
            echo "<p>". __("There are currently no surveys available, check back soon for new content.", 'mc_survey') ."</p>";

		endif; ?>
        </div>
<?php get_footer(); ?>
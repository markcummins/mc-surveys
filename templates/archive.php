<?php get_header(); ?>

        <div class="mc-survey-archive">
            <h1><?php _e('Surveys', 'mc-survey'); ?></h1>

            <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post(); ?>

                    <article id="post-<?php the_ID(); ?>">
                        <header class="mc-survey-archive-thumb">
                            <?php if ( has_post_thumbnail() ): ?>
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                            <?php else: ?>
                                <?php do_action('mc_get_default_thumbnail'); ?>                                
                            <?php endif; ?>
                        </header><div class="mc-survey-archive-meta">
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                               
                                <?php the_excerpt(); ?>
                                    
                                <?php $settings_meta = get_post_meta(get_the_id(), '_mc_options', true); ?>
                                    <i><?php the_time('F j, Y'); ?></i>
                                <?php if(is_array($settings_meta)): ?>
                                    <?php if(array_key_exists('member-only', $settings_meta)) echo "<i> | ". __('Members Only', 'mc-survey') ."</i>"; ?>
                                    <?php if(array_key_exists('closed', $settings_meta)) echo "<i> | ". __('Survey Closed', 'mc-survey') ."</i>"; ?>
                                <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile;

                // Previous/next page navigation.
                the_posts_pagination( array(
                    'prev_text'          => __( 'Previous page', 'mc_survey' ),
                    'next_text'          => __( 'Next page', 'mc_survey' ),
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'mc_survey' ) . ' </span>',
                ));
            else : ?>
                <article>
                    <h3><?php _e("No surveys available", 'mc_survey'); ?></h3>
                    <p><?php _e("There are currently no surveys available, check back soon for new content.", 'mc_survey'); ?></p>
                </article>
            <?php endif; ?>
        </div>
<?php get_footer(); ?>
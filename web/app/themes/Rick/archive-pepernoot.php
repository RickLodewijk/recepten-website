<?php
get_header();
?>
<section class="home-hero">
    <div class="container">
        <h2>Pepernoten ranken</h2>
        <p>Alle pepernoten op een rij, gesorteerd op jouw ranking.</p>
    </div>
</section>

<section class="recipe-grid-section">
    <div class="container">
        <?php
        $query = new WP_Query(array(
            'post_type' => 'pepernoot',
            'posts_per_page' => -1,
            'meta_key' => 'pepernoot_score',
            'orderby' => array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC',
            ),
        ));
        ?>

        <?php if ( $query->have_posts() ) : ?>
            <div class="recipe-grid">
                <?php while ( $query->have_posts() ) : $query->the_post();
                    $score = get_post_meta( get_the_ID(), 'pepernoot_score', true );
                    $subtitle = get_post_meta( get_the_ID(), 'pepernoot_subtitle', true );
                    ?>
                    <a href="<?php the_permalink(); ?>" class="recipe-card">
                        <div class="recipe-card__image">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium' ); ?>
                            <?php else : ?>
                                <div class="placeholder-image">
                                    <span>🍪</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="recipe-card__content">
                            <?php if ( $score !== '' ) : ?>
                                <span class="recipe-card__category">Score <?php echo esc_html( $score ); ?>/10</span>
                            <?php endif; ?>
                            <h3 class="recipe-card__title"><?php the_title(); ?></h3>
                            <?php if ( $subtitle ) : ?>
                                <p class="recipe-card__excerpt"><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="no-results">Nog geen pepernoten toegevoegd.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer();

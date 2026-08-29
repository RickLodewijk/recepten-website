<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $score = get_field( 'pepernoot_score' );
        $subtitle = get_field( 'pepernoot_subtitle' );
        $image = get_field( 'pepernoot_afbeelding' );
        $intro = get_field( 'pepernoot_intro' );
        $pluspunten = get_field( 'pepernoot_pluspunten' );
        $minpunten = get_field( 'pepernoot_minpunten' );
        ?>
        <article class="recipe-container pepernoot-container">
            <div class="header<?php echo $image ? ' has-image' : ''; ?>">
                <div class="content">
                    <?php if ( $subtitle ) : ?>
                        <p class="meta-info"><?php echo esc_html( $subtitle ); ?></p>
                    <?php endif; ?>

                    <h1><?php the_title(); ?></h1>

                    <?php if ( $score !== '' ) : ?>
                        <span class="badge">Score: <?php echo esc_html( $score ); ?>/10</span>
                    <?php endif; ?>
                </div>

                <?php if ( $image ) : ?>
                    <div class="image-wrapper">
                        <img class="image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $intro ) : ?>
                <div class="intro">
                    <p><?php echo wp_kses_post( wpautop( $intro ) ); ?></p>
                </div>
            <?php endif; ?>

            <?php if ( $pluspunten ) : ?>
                <details class="pepernoot-notes pepernoot-notes--plus pepernoot-collapsible" open>
                    <summary class="pepernoot-collapsible-summary">
                        <h2><span>✓</span> Pluspunten</h2>
                        <span class="pepernoot-collapsible-arrow"></span>
                    </summary>
                    <div class="pepernoot-collapsible-content">
                        <?php foreach ( preg_split( '/\r\n|\r|\n/', trim( $pluspunten ) ) as $line ) : ?>
                            <?php if ( trim( $line ) !== '' ) : ?>
                                <p><?php echo esc_html( trim( $line ) ); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endif; ?>

            <?php if ( $minpunten ) : ?>
                <details class="pepernoot-notes pepernoot-notes--min pepernoot-collapsible" open>
                    <summary class="pepernoot-collapsible-summary">
                        <h2><span>✕</span> Minpunten</h2>
                        <span class="pepernoot-collapsible-arrow"></span>
                    </summary>
                    <div class="pepernoot-collapsible-content">
                        <?php foreach ( preg_split( '/\r\n|\r|\n/', trim( $minpunten ) ) as $line ) : ?>
                            <?php if ( trim( $line ) !== '' ) : ?>
                                <p><?php echo esc_html( trim( $line ) ); ?></p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </details>
            <?php endif; ?>
        </article>
        <?php
    endwhile;
endif;

get_footer();

<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $meta_info = rick_get_recept_field('meta_info');
        $recept_afbeelding = rick_get_recept_field('recept_afbeelding');
        $bereidingstijd = rick_get_recept_field('bereidingstijd');
        $intro_tekst = rick_get_recept_field('intro_tekst');
        $ingredienten = rick_get_recept_field('ingredienten');
        $bereidingswijze = rick_get_recept_field('bereidingswijze');
        $bakker_tip = rick_get_recept_field('bakker_tip');
        ?>
        <div class="recipe-container">
            <div class="header<?php echo $recept_afbeelding ? ' has-image' : ''; ?>">
                <div class="content">
                    <?php if ( $meta_info ) : ?>
                        <p class="meta-info"><?php echo esc_html( $meta_info ); ?></p>
                    <?php endif; ?>

                    <h1><?php the_title(); ?></h1>

                    <?php if ( $bereidingstijd ) : ?>
                        <span class="badge">Totale bereidingstijd: <?php echo esc_html( $bereidingstijd ); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ( $recept_afbeelding ) : ?>
                    <div class="image-wrapper">
                        <img class="image" src="<?php echo esc_url( $recept_afbeelding ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $intro_tekst ) : ?>
                <div class="intro">
                    <p><?php echo wp_kses_post( wpautop( $intro_tekst ) ); ?></p>
                </div>
            <?php endif; ?>

            <?php if ( $ingredienten ) : ?>
                <h2>Ingrediënten</h2>
                <table class="ingredients-table">
                    <thead>
                        <tr>
                            <th>Ingrediënt</th>
                            <th>Hoeveelheid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ingredient_lines = preg_split('/\r\n|\r|\n/', trim( $ingredienten ) );

                        foreach ( $ingredient_lines as $ingredient_line ) {
                            $ingredient_line = wp_strip_all_tags( trim( $ingredient_line ) );

                            if ( $ingredient_line === '' ) {
                                continue;
                            }

                            $parts = array_map('trim', explode('|', $ingredient_line, 2));
                            $ingredient_name = wp_strip_all_tags( $parts[0] );
                            $ingredient_amount = wp_strip_all_tags( $parts[1] ?? '' );

                            echo '<tr>';
                            echo '<td>' . esc_html( $ingredient_name ) . '</td>';
                            echo '<td>' . esc_html( $ingredient_amount ) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ( $bereidingswijze ) : ?>
                <h2>Bereidingswijze</h2>
                <div class="steps">
                    <?=$bereidingswijze?>
                </div>
            <?php endif; ?>

            <?php if ( $bakker_tip ) : ?>
                <div class="tip-box">
                    <div class="tip-title">💡 Tip van de bakker:</div>
                    <p class="tip-content"><?php echo wp_kses_post( wpautop( $bakker_tip ) ); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    endwhile;
endif;

get_footer();
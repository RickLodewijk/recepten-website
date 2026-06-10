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
        <main class="recipe-single">
            <article class="recipe-single__card">
                <header class="recipe-single__header<?php echo $recept_afbeelding ? ' recipe-single__header--with-image' : ''; ?>">
                    <div class="recipe-single__header-content">
                        <?php if ( $meta_info ) : ?>
                            <p class="recipe-single__meta"><?php echo esc_html( $meta_info ); ?></p>
                        <?php endif; ?>

                        <h1 class="recipe-single__title"><?php the_title(); ?></h1>

                        <?php if ( $bereidingstijd ) : ?>
                            <span class="recipe-single__badge">Totale bereidingstijd: <?php echo esc_html( $bereidingstijd ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $recept_afbeelding ) : ?>
                        <div class="recipe-single__image-wrap">
                            <img class="recipe-single__image" src="<?php echo esc_url( $recept_afbeelding ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                        </div>
                    <?php endif; ?>
                </header>

                <?php if ( $intro_tekst ) : ?>
                    <section class="recipe-single__section recipe-single__intro">
                        <?=$intro_tekst ?>
                    </section>
                <?php endif; ?>

                <?php if ( $ingredienten ) : ?>
                    <section class="recipe-single__section">
                        <h2>Ingrediënten</h2>
                        <div class="recipe-single__ingredients">
                            <table class="recipe-single__ingredients-table">
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
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ( $bereidingswijze ) : ?>
                    <section class="recipe-single__section">
                        <h2>Bereidingswijze</h2>
                        <div class="recipe-single__steps">
                            <?=$bereidingswijze?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ( $bakker_tip ) : ?>
                    <aside class="recipe-single__tip">
                        <div class="recipe-single__tip-title">Bakker Tip</div>
                        <div class="recipe-single__tip-content"><?php echo wp_kses_post( wpautop( $bakker_tip ) ); ?></div>
                    </aside>
                <?php endif; ?>
            </article>
        </main>
        <?php
    endwhile;
endif;

get_footer();
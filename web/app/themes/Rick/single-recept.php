<?php
require_once get_theme_file_path('inc/recepten/helpers.php');

get_header();

if (have_posts()):
    while (have_posts()):
        the_post();
        $meta_info = rick_get_recept_field('meta_info');
        $recept_afbeelding = rick_get_recept_field('recept_afbeelding');
        $bereidingstijd = rick_get_recept_field('bereidingstijd');
        $intro_tekst = rick_get_recept_field('intro_tekst');
        $ingredienten = rick_get_recept_field('ingredienten');
        $bereidingswijze = rick_get_recept_field('bereidingswijze');
        $bakker_tip = rick_get_recept_field('bakker_tip');
        $recipe_color = rick_get_recept_primary_category_color(get_the_ID());

        $kcal = rick_get_recept_field('kcal');
        $eiwitten = rick_get_recept_field('eiwitten');
        $koolhydraten = rick_get_recept_field('koolhydraten');
        $vetten = rick_get_recept_field('vetten');
        ?>
        <div class="recipe-container" style="--primary-color: <?php echo esc_attr($recipe_color); ?>;">
            <div class="recipe-print-actions">
                <div class="recipe-categories">
                    <?php
                    $terms = get_the_terms(get_the_ID(), 'recept_categorie');
                    if (!empty($terms) && !is_wp_error($terms)):
                        foreach ($terms as $term):
                            $color = rick_get_term_color($term);
                            ?>
                            <span class="category-pill" style="background-color: <?php echo esc_attr($color); ?>;">
                                <?php echo esc_html($term->name); ?>
                            </span>
                        <?php endforeach;
                    endif;
                    ?>
                </div>
                <button class="recipe-print-actions__button" type="button" onclick="window.print()">Print als fotoboek</button>
            </div>

            <div class="header<?php echo $recept_afbeelding ? ' has-image' : ''; ?>">
                <div class="content">
                    <?php if ($meta_info): ?>
                        <p class="meta-info"><?php echo esc_html($meta_info); ?></p>
                    <?php endif; ?>

                    <h1><?php the_title(); ?></h1>

                    <?php if ($bereidingstijd): ?>
                        <span class="badge">Totale bereidingstijd: <?php echo esc_html($bereidingstijd); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ($recept_afbeelding): ?>
                    <div class="image-wrapper">
                        <img class="image" src="<?php echo esc_url($recept_afbeelding); ?>"
                            alt="<?php echo esc_attr(get_the_title()); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($intro_tekst): ?>
                <div class="intro">
                    <p><?php echo wp_kses_post(wpautop($intro_tekst)); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($ingredienten): ?>
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
                        $ingredient_lines = preg_split('/\r\n|\r|\n/', trim($ingredienten));

                        foreach ($ingredient_lines as $ingredient_line) {
                            $ingredient_line = wp_strip_all_tags(trim($ingredient_line));

                            if ($ingredient_line === '') {
                                continue;
                            }

                            $parts = array_map('trim', explode('|', $ingredient_line, 2));
                            $ingredient_name = wp_strip_all_tags($parts[0]);
                            $ingredient_amount = wp_strip_all_tags($parts[1] ?? '');

                            echo '<tr>';
                            echo '<td>' . esc_html($ingredient_name) . '</td>';
                            echo '<td>' . esc_html($ingredient_amount) . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($kcal || $eiwitten || $koolhydraten || $vetten): ?>
                <h2>Voedingswaarden per 100 gram</h2>
                <div class="nutrition">
                    <?php if ($kcal): ?>
                        <p><strong>Energie:</strong> <?= ($kcal) ?> kcal</p>
                    <?php endif; ?>
                    <?php if ($eiwitten): ?>
                        <p><strong>Eiwitten:</strong> <?= esc_html($eiwitten) ?> g</p>
                    <?php endif; ?>
                    <?php if ($koolhydraten): ?>
                        <p><strong>Koolhydraten:</strong> <?= esc_html($koolhydraten) ?> g</p>
                    <?php endif; ?>
                    <?php if ($vetten): ?>
                        <p><strong>Vetten:</strong> <?= esc_html($vetten) ?> g</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($bereidingswijze): ?>
                <h2>Bereidingswijze</h2>
                <div class="steps">
                    <?= $bereidingswijze ?>
                </div>
            <?php endif; ?>

            <?php if ($bakker_tip): ?>
                <div class="tip-box">
                    <div class="tip-title">💡 Tip van de bakker:</div>
                    <p class="tip-content"><?php echo wp_kses_post(wpautop($bakker_tip)); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    endwhile;
endif;

get_footer();
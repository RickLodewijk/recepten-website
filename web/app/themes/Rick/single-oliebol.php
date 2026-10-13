<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        $post_id    = get_the_ID();
        $locatie    = function_exists('get_field') ? get_field('oliebol_locatie', $post_id) : get_post_meta($post_id, 'oliebol_locatie', true);
        $kraam      = function_exists('get_field') ? get_field('oliebol_kraam', $post_id) : get_post_meta($post_id, 'oliebol_kraam', true);
        $smaak      = function_exists('get_field') ? get_field('oliebol_smaak', $post_id) : get_post_meta($post_id, 'oliebol_smaak', true);
        $auteur     = function_exists('get_field') ? get_field('oliebol_auteur', $post_id) : get_post_meta($post_id, 'oliebol_auteur', true);
        $cijfer_raw = function_exists('get_field') ? get_field('oliebol_cijfer', $post_id) : get_post_meta($post_id, 'oliebol_cijfer', true);
        $prijs      = function_exists('get_field') ? get_field('oliebol_prijs', $post_id) : get_post_meta($post_id, 'oliebol_prijs', true);
        $image      = function_exists('get_field') ? get_field('oliebol_afbeelding', $post_id) : get_post_meta($post_id, 'oliebol_afbeelding', true);
        $omschrijving = function_exists('get_field') ? get_field('oliebol_omschrijving', $post_id) : get_post_meta($post_id, 'oliebol_omschrijving', true);

        if ( empty($image) && has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url($post_id, 'large');
        }

        $cijfer = floatval($cijfer_raw);
        $volle_sterren = (int) floor($cijfer / 2);
        $halve_sterren = (($cijfer / 2) - $volle_sterren >= 0.4) ? 1 : 0;
        $lege_sterren  = max(0, 5 - $volle_sterren - $halve_sterren);
        ?>
        <article class="recipe-container oliebol-container">
            <div class="recipe-print-actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('oliebol')); ?>" class="recipe-print-actions__button" style="text-decoration:none;">
                    &larr; Terug naar overzicht
                </a>
            </div>

            <div class="header<?php echo $image ? ' has-image' : ''; ?>">
                <div class="content">
                    <?php if ( ! empty($locatie) ) : ?>
                        <p class="meta-info">📍 <?php echo esc_html($locatie); ?><?php echo ! empty($kraam) ? ' &bull; ' . esc_html($kraam) : ''; ?></p>
                    <?php endif; ?>

                    <h1><?php the_title(); ?></h1>

                    <div class="recipe-meta-badges">
                        <?php if ( $cijfer_raw !== '' && $cijfer_raw !== null ) : ?>
                            <span class="badge" style="background:#fef3c7;color:#92400e;">
                                ⭐ Score: <?php echo esc_html(number_format($cijfer, 1, ',', '')); ?>/10
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty($smaak) ) : ?>
                            <span class="badge" style="background:#fdf2f8;color:#9d174d;">
                                🥐 Smaak: <?php echo esc_html($smaak); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty($prijs) ) : ?>
                            <span class="badge" style="background:#ecfdf5;color:#065f46;">
                                💶 €<?php echo esc_html(str_replace('€', '', $prijs)); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty($auteur) ) : ?>
                            <span class="badge badge--author" style="background:#f3f4f6;color:#374151;">
                                ✍️ Geschreven door: <?php echo esc_html($auteur); ?>
                            </span>
                        <?php endif; ?>

                        <span class="badge" style="background:#f8fafc;color:#64748b;">
                            📅 Ingevuld op: <?php echo esc_html(get_the_date('j-n-Y H:i')); ?>
                        </span>
                    </div>

                    <!-- Sterrenweergave -->
                    <div style="margin-top:14px;color:#f59e0b;font-size:1.4rem;letter-spacing:2px;">
                        <?php
                        for ($i = 0; $i < $volle_sterren; $i++) {
                            echo '<span>★</span>';
                        }
                        if ($halve_sterren) {
                            echo '<span>★</span>';
                        }
                        for ($i = 0; $i < $lege_sterren; $i++) {
                            echo '<span style="color:#d1d5db;">☆</span>';
                        }
                        ?>
                    </div>
                </div>

                <?php if ( $image ) : ?>
                    <div class="image-wrapper">
                        <img class="image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( $omschrijving ) : ?>
                <div class="intro" style="margin-top:30px;">
                    <h2>Beoordeling & Review</h2>
                    <p><?php echo wp_kses_post(wpautop($omschrijving)); ?></p>
                </div>
            <?php elseif ( get_the_content() ) : ?>
                <div class="intro" style="margin-top:30px;">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </article>
        <?php
    endwhile;
endif;

get_footer();

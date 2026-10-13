<?php
/**
 * Render template for rick/oliebol-card block.
 * Gekoppeld aan het Oliebol Custom Post Type (CPT).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$oliebol_id = isset($attributes['oliebolId']) ? intval($attributes['oliebolId']) : 0;
$button_text_override = $attributes['buttonText'] ?? 'Bekijk Volledige Review';

if ($oliebol_id <= 0) {
    $recent_posts = get_posts([
        'post_type'      => 'oliebol',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if (!empty($recent_posts)) {
        $oliebol_id = $recent_posts[0]->ID;
    }
}

$oliebol_post = $oliebol_id > 0 ? get_post($oliebol_id) : null;

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-oliebol-review-section',
]);

if (!$oliebol_post) {
    ?>
    <section <?php echo $wrapper_attributes; ?>>
        <div class="container">
            <div class="rick-oliebol-review-card" style="text-align: center; padding: 40px 20px;">
                <p style="color: #92400e; font-weight: 700; font-size: 1.1rem; margin: 0 0 8px 0;">🥐 Geen Oliebol Gevonden</p>
                <p style="color: #6b7280; margin: 0;">Er is nog geen oliebol review gepubliceerd in de database.</p>
            </div>
        </div>
    </section>
    <?php
    return;
}

$title        = get_the_title($oliebol_id);
$permalink    = get_permalink($oliebol_id);
$locatie      = function_exists('get_field') ? get_field('oliebol_locatie', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_locatie', true);
$kraam        = function_exists('get_field') ? get_field('oliebol_kraam', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_kraam', true);
$smaak        = function_exists('get_field') ? get_field('oliebol_smaak', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_smaak', true);
$auteur       = function_exists('get_field') ? get_field('oliebol_auteur', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_auteur', true);
$cijfer_raw   = function_exists('get_field') ? get_field('oliebol_cijfer', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_cijfer', true);
$prijs        = function_exists('get_field') ? get_field('oliebol_prijs', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_prijs', true);
$image_url    = function_exists('get_field') ? get_field('oliebol_afbeelding', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_afbeelding', true);
$omschrijving = function_exists('get_field') ? get_field('oliebol_omschrijving', $oliebol_id) : get_post_meta($oliebol_id, 'oliebol_omschrijving', true);

if (empty($image_url) && has_post_thumbnail($oliebol_id)) {
    $image_url = get_the_post_thumbnail_url($oliebol_id, 'large');
}

if (empty($omschrijving) && !empty($oliebol_post->post_content)) {
    $omschrijving = wp_trim_words(strip_shortcodes($oliebol_post->post_content), 30, '...');
}

$cijfer = floatval($cijfer_raw);
$volle_sterren = (int) floor($cijfer / 2);
$halve_sterren = (($cijfer / 2) - $volle_sterren >= 0.4) ? 1 : 0;
$lege_sterren  = max(0, 5 - $volle_sterren - $halve_sterren);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <article class="rick-oliebol-review-card">
            <div class="rick-oliebol-review-header">
                <div class="rick-oliebol-review-header__left">
                    <div class="rick-oliebol-review-badges">
                        <?php if (!empty($locatie)) : ?>
                            <span class="rick-review-badge rick-review-badge--locatie">📍 <?php echo esc_html($locatie); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($smaak)) : ?>
                            <span class="rick-review-badge rick-review-badge--smaak">🥐 <?php echo esc_html($smaak); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($kraam)) : ?>
                            <span class="rick-review-badge rick-review-badge--kraam">🎪 <?php echo esc_html($kraam); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($prijs)) : ?>
                            <span class="rick-review-badge rick-review-badge--price">💶 €<?php echo esc_html(str_replace('€', '', $prijs)); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($auteur)) : ?>
                            <span class="rick-review-badge rick-review-badge--author">✍️ <?php echo esc_html($auteur); ?></span>
                        <?php endif; ?>
                    </div>

                    <h2 class="rick-oliebol-review-title">
                        <a href="<?php echo esc_url($permalink); ?>" style="color: inherit; text-decoration: none;">
                            <?php echo wp_kses_post($title); ?>
                        </a>
                    </h2>

                    <!-- Sterren -->
                    <div class="rick-oliebol-stars">
                        <?php
                        for ($i = 0; $i < $volle_sterren; $i++) {
                            echo '<span class="star star--full">★</span>';
                        }
                        if ($halve_sterren) {
                            echo '<span class="star star--half">★</span>';
                        }
                        for ($i = 0; $i < $lege_sterren; $i++) {
                            echo '<span class="star star--empty">☆</span>';
                        }
                        ?>
                    </div>
                </div>

                <?php if ($cijfer_raw !== '' && $cijfer_raw !== null) : ?>
                    <div class="rick-oliebol-score-circle">
                        <span class="rick-oliebol-score-star">★</span>
                        <span class="rick-oliebol-score-number"><?php echo esc_html(number_format($cijfer, 1, ',', '')); ?></span>
                        <span class="rick-oliebol-score-max">/10</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="rick-oliebol-review-body">
                <?php if (!empty($image_url)) : ?>
                    <div class="rick-oliebol-review-media">
                        <a href="<?php echo esc_url($permalink); ?>" tabindex="-1">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                        </a>
                    </div>
                <?php endif; ?>

                <div class="rick-oliebol-review-content">
                    <?php if (!empty($omschrijving)) : ?>
                        <div class="rick-oliebol-review-intro">
                            <p><?php echo nl2br(wp_kses_post($omschrijving)); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rick-oliebol-card-footer">
                <a href="<?php echo esc_url($permalink); ?>" class="button rick-oliebol-card-button">
                    <span><?php echo esc_html($button_text_override); ?></span>
                    <span class="rick-button-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </article>
    </div>
</section>

<?php
/**
 * Render template for rick/pepernoot-card block.
 * Gekoppeld aan het Pepernoot Custom Post Type (CPT).
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$pepernoot_id = isset($attributes['pepernootId']) ? intval($attributes['pepernootId']) : 0;
$button_text_override = $attributes['buttonText'] ?? 'Bekijk Volledige Review';

// Indien geen specifieke pepernoot gekozen is (0), pak de meest recente gepubliceerde pepernoot
if ($pepernoot_id <= 0) {
    $recent_posts = get_posts([
        'post_type'      => 'pepernoot',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if (!empty($recent_posts)) {
        $pepernoot_id = $recent_posts[0]->ID;
    }
}

// Haal pepernoot post op
$pepernoot_post = $pepernoot_id > 0 ? get_post($pepernoot_id) : null;

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-pepernoot-review-section',
]);

if (!$pepernoot_post) {
    ?>
    <section <?php echo $wrapper_attributes; ?>>
        <div class="container">
            <div class="rick-pepernoot-review-card" style="text-align: center; padding: 40px 20px;">
                <p style="color: #92400e; font-weight: 700; font-size: 1.1rem; margin: 0 0 8px 0;">🍪 Geen Pepernoot Gevonden</p>
                <p style="color: #6b7280; margin: 0;">Er is nog geen pepernoot review gepubliceerd in de database.</p>
            </div>
        </div>
    </section>
    <?php
    return;
}

// Haal velden op uit de Pepernoot CPT
$title       = get_the_title($pepernoot_id);
$permalink   = get_permalink($pepernoot_id);
$subtitle    = function_exists('get_field') ? get_field('pepernoot_subtitle', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_subtitle', true);
$score       = function_exists('get_field') ? get_field('pepernoot_score', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_score', true);
$brand       = function_exists('get_field') ? get_field('pepernoot_brand', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_brand', true);
$shop        = function_exists('get_field') ? get_field('pepernoot_shop', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_shop', true);
$price       = function_exists('get_field') ? get_field('pepernoot_price', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_price', true);
$pro         = function_exists('get_field') ? get_field('pepernoot_pro', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_pro', true);
$con         = function_exists('get_field') ? get_field('pepernoot_con', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_con', true);
$pluspunten  = function_exists('get_field') ? get_field('pepernoot_pluspunten', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_pluspunten', true);
$minpunten   = function_exists('get_field') ? get_field('pepernoot_minpunten', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_minpunten', true);
$intro       = function_exists('get_field') ? get_field('pepernoot_intro', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_intro', true);
$image_url   = function_exists('get_field') ? get_field('pepernoot_afbeelding', $pepernoot_id) : get_post_meta($pepernoot_id, 'pepernoot_afbeelding', true);

if (empty($image_url) && has_post_thumbnail($pepernoot_id)) {
    $image_url = get_the_post_thumbnail_url($pepernoot_id, 'large');
}

if (empty($intro) && !empty($pepernoot_post->post_content)) {
    $intro = wp_trim_words(strip_shortcodes($pepernoot_post->post_content), 30, '...');
}

// Array van regels maken voor plus/minpunten
$plus_items = !empty($pluspunten) ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$pluspunten))) : [];
$min_items  = !empty($minpunten) ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$minpunten))) : [];
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <article class="rick-pepernoot-review-card">
            <!-- Header met Titel, Meta & Score -->
            <div class="rick-pepernoot-review-header">
                <div class="rick-pepernoot-review-header__left">
                    <div class="rick-pepernoot-review-badges">
                        <?php if (!empty($brand)) : ?>
                            <span class="rick-review-badge rick-review-badge--brand">🏷️ <?php echo esc_html($brand); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($shop)) : ?>
                            <span class="rick-review-badge rick-review-badge--shop">🛒 <?php echo esc_html($shop); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($price)) : ?>
                            <span class="rick-review-badge rick-review-badge--price">💶 <?php echo esc_html($price); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($title)) : ?>
                        <h2 class="rick-pepernoot-review-title">
                            <a href="<?php echo esc_url($permalink); ?>" style="color: inherit; text-decoration: none;">
                                <?php echo wp_kses_post($title); ?>
                            </a>
                        </h2>
                    <?php endif; ?>

                    <?php if (!empty($subtitle)) : ?>
                        <p class="rick-pepernoot-review-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($score)) : ?>
                    <div class="rick-pepernoot-score-circle">
                        <span class="rick-pepernoot-score-star">★</span>
                        <span class="rick-pepernoot-score-number"><?php echo esc_html($score); ?></span>
                        <span class="rick-pepernoot-score-max">/10</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Body met Afbeelding & Review tekst -->
            <div class="rick-pepernoot-review-body">
                <?php if (!empty($image_url)) : ?>
                    <div class="rick-pepernoot-review-media">
                        <a href="<?php echo esc_url($permalink); ?>" tabindex="-1">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                        </a>
                    </div>
                <?php endif; ?>

                <div class="rick-pepernoot-review-content">
                    <?php if (!empty($intro)) : ?>
                        <div class="rick-pepernoot-review-intro">
                            <p><?php echo nl2br(wp_kses_post($intro)); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Korte Highlights -->
                    <?php if (!empty($pro) || !empty($con)) : ?>
                        <div class="rick-pepernoot-highlights">
                            <?php if (!empty($pro)) : ?>
                                <div class="rick-highlight-pill rick-highlight-pill--pro">
                                    <span class="rick-highlight-icon">👍</span>
                                    <span><strong>Pluspunt:</strong> <?php echo esc_html($pro); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($con)) : ?>
                                <div class="rick-highlight-pill rick-highlight-pill--con">
                                    <span class="rick-highlight-icon">👎</span>
                                    <span><strong>Minpunt:</strong> <?php echo esc_html($con); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Plus- en Minpunten Overzicht (Inklapbaar) -->
            <?php if (!empty($plus_items) || !empty($min_items)) : ?>
                <div class="rick-pepernoot-pros-cons-grid">
                    <?php if (!empty($plus_items)) : ?>
                        <details class="rick-pros-box rick-collapsible" open>
                            <summary class="rick-pros-box__title rick-collapsible-summary">
                                <div class="rick-collapsible-title-wrap">
                                    <span class="rick-pros-icon">✓</span>
                                    <span>Pluspunten (<?php echo count($plus_items); ?>)</span>
                                </div>
                                <span class="rick-collapsible-chevron" aria-hidden="true"></span>
                            </summary>
                            <div class="rick-collapsible-body">
                                <ul class="rick-pros-list">
                                    <?php foreach ($plus_items as $item) : ?>
                                        <li><?php echo esc_html($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                    <?php endif; ?>

                    <?php if (!empty($min_items)) : ?>
                        <details class="rick-cons-box rick-collapsible" open>
                            <summary class="rick-cons-box__title rick-collapsible-summary">
                                <div class="rick-collapsible-title-wrap">
                                    <span class="rick-cons-icon">✕</span>
                                    <span>Minpunten (<?php echo count($min_items); ?>)</span>
                                </div>
                                <span class="rick-collapsible-chevron" aria-hidden="true"></span>
                            </summary>
                            <div class="rick-collapsible-body">
                                <ul class="rick-cons-list">
                                    <?php foreach ($min_items as $item) : ?>
                                        <li><?php echo esc_html($item); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </details>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Actieknop die linkt naar de Pepernoot single pagina -->
            <div class="rick-pepernoot-card-footer">
                <a href="<?php echo esc_url($permalink); ?>" class="button rick-pepernoot-card-button">
                    <span><?php echo esc_html($button_text_override); ?></span>
                    <span class="rick-button-arrow" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </article>
    </div>
</section>

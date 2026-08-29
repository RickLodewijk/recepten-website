<?php
/**
 * Render template for rick/pepernoot-card block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$score = $attributes['score'] ?? '';
$brand = $attributes['brand'] ?? '';
$shop = $attributes['shop'] ?? '';
$price = $attributes['price'] ?? '';
$image_url = $attributes['imageUrl'] ?? '';
$image_alt = $attributes['imageAlt'] ?? $title;
$pro = $attributes['pro'] ?? '';
$con = $attributes['con'] ?? '';
$pluspunten = $attributes['pluspunten'] ?? '';
$minpunten = $attributes['minpunten'] ?? '';
$intro = $attributes['intro'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url = $attributes['buttonUrl'] ?? '';

// Array van regels maken voor plus/minpunten
$plus_items = !empty($pluspunten) ? array_filter(array_map('trim', explode("\n", $pluspunten))) : [];
$min_items = !empty($minpunten) ? array_filter(array_map('trim', explode("\n", $minpunten))) : [];

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-pepernoot-review-section',
]);
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
                        <h2 class="rick-pepernoot-review-title"><?php echo wp_kses_post($title); ?></h2>
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
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
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

            <!-- Plus- en Minpunten Overzicht (Inklapbaar op mobiel) -->
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

            <!-- Optionele Actieknop -->
            <?php if (!empty($button_text) && !empty($button_url)) : ?>
                <div class="rick-pepernoot-card-footer">
                    <a href="<?php echo esc_url($button_url); ?>" class="button rick-pepernoot-card-button">
                        <span><?php echo esc_html($button_text); ?></span>
                        <span class="rick-button-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            <?php endif; ?>
        </article>
    </div>
</section>

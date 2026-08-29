<?php
/**
 * Render template for rick/gallery-slider block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? '';
$items = $attributes['items'] ?? [];

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-gallery-slider-section',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <?php if (!empty($title)) : ?>
            <h2 class="rick-slider-title">
                <span class="rick-slider-icon" aria-hidden="true">🍳</span>
                <?php echo wp_kses_post($title); ?>
            </h2>
        <?php endif; ?>

        <?php if (!empty($items) && is_array($items)) : ?>
            <div class="rick-slider-container">
                <?php foreach ($items as $item) :
                    $image_url = $item['imageUrl'] ?? '';
                    $label = $item['label'] ?? '';
                    $item_url = $item['url'] ?? '#';
                    $image_alt = $item['imageAlt'] ?? $label;
                ?>
                    <a href="<?php echo esc_url($item_url); ?>" class="rick-slider-item">
                        <?php if (!empty($image_url)) : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy" />
                        <?php endif; ?>

                        <?php if (!empty($label)) : ?>
                            <div class="rick-slider-badge">
                                <span><?php echo esc_html($label); ?></span>
                                <span class="rick-slider-badge-icon" aria-hidden="true">&nearr;</span>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

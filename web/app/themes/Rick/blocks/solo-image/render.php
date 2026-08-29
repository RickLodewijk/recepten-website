<?php
/**
 * Render template for rick/solo-image block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$image_url = $attributes['imageUrl'] ?? '';
$image_alt = $attributes['imageAlt'] ?? '';
$overlay_title = $attributes['overlayTitle'] ?? '';
$height = $attributes['height'] ?? '350px';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-solo-image-section',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <div class="rick-solo-image-block" style="height: <?php echo esc_attr($height); ?>;">
            <?php if (!empty($image_url)) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt ?: $overlay_title); ?>" loading="lazy" />
            <?php endif; ?>

            <?php if (!empty($overlay_title)) : ?>
                <div class="rick-solo-image-overlay">
                    <h2><?php echo wp_kses_post($overlay_title); ?></h2>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

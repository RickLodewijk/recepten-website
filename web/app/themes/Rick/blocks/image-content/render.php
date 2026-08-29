<?php
/**
 * Render template for rick/image-content block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$image_url = $attributes['imageUrl'] ?? '';
$image_alt = $attributes['imageAlt'] ?? '';
$image_position = $attributes['imagePosition'] ?? 'left';
$title = $attributes['title'] ?? '';
$text_content = $attributes['content'] ?? '';
$button_text = $attributes['buttonText'] ?? '';
$button_url = $attributes['buttonUrl'] ?? '';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-image-content-section',
]);

$order_class = ($image_position === 'right') ? 'is-image-right' : 'is-image-left';
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <div class="rick-image-content-block <?php echo esc_attr($order_class); ?>">
            <?php if (!empty($image_url)) : ?>
                <div class="rick-image-content-media">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt ?: $title); ?>" loading="lazy" />
                </div>
            <?php endif; ?>

            <div class="rick-image-content-text">
                <?php if (!empty($title)) : ?>
                    <h2><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($text_content)) : ?>
                    <div class="rick-image-content-body">
                        <?php echo wp_kses_post(wpautop($text_content)); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($button_text) && !empty($button_url)) : ?>
                    <div class="rick-image-content-actions">
                        <a href="<?php echo esc_url($button_url); ?>" class="button rick-btn-primary">
                            <?php echo esc_html($button_text); ?> <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

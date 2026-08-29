<?php
/**
 * Render template for rick/pageheader block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$badge = $attributes['badge'] ?? '';
$text_align = $attributes['textAlign'] ?? 'center';
$bg_image_url = $attributes['bgImageUrl'] ?? '';
$overlay_type = $attributes['overlayType'] ?? 'warm-dark';
$min_height = $attributes['minHeight'] ?? 'medium';

// Knoppen repeater (met fallback voor eerdere enkele knop)
$buttons = $attributes['buttons'] ?? [];
if (empty($buttons) && !empty($attributes['buttonText'])) {
    $buttons = [
        [
            'text' => $attributes['buttonText'],
            'url' => $attributes['buttonUrl'] ?? '#',
            'style' => 'primary',
        ],
    ];
}

$align_class = in_array($text_align, ['left', 'center', 'right'], true) ? 'is-align-' . $text_align : 'is-align-center';
$height_class = 'is-height-' . sanitize_html_class($min_height);
$has_bg_class = !empty($bg_image_url) ? 'has-bg-image overlay-' . sanitize_html_class($overlay_type) : 'no-bg-image';

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-pageheader ' . $align_class . ' ' . $height_class . ' ' . $has_bg_class,
    'style' => !empty($bg_image_url) ? '--rick-header-bg: url(' . esc_url($bg_image_url) . ');' : '',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="rick-pageheader__overlay"></div>
    <div class="container rick-pageheader__container">
        <?php if (!empty($badge)) : ?>
            <div class="rick-pageheader__badge-wrapper">
                <span class="rick-pageheader__badge"><?php echo esc_html($badge); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($title)) : ?>
            <h1 class="rick-pageheader__title"><?php echo wp_kses_post($title); ?></h1>
        <?php endif; ?>

        <?php if (!empty($subtitle)) : ?>
            <p class="rick-pageheader__subtitle"><?php echo wp_kses_post($subtitle); ?></p>
        <?php endif; ?>

        <?php if (!empty($buttons) && is_array($buttons)) : ?>
            <div class="rick-pageheader__actions">
                <?php foreach ($buttons as $btn) :
                    $btn_text = $btn['text'] ?? '';
                    $btn_url = $btn['url'] ?? '#';
                    $btn_style = $btn['style'] ?? 'primary';
                    if (empty($btn_text)) continue;

                    $btn_class = 'button rick-pageheader__button rick-pageheader__button--' . sanitize_html_class($btn_style);
                ?>
                    <a href="<?php echo esc_url($btn_url); ?>" class="<?php echo esc_attr($btn_class); ?>">
                        <span><?php echo esc_html($btn_text); ?></span>
                        <span class="rick-pageheader__button-arrow" aria-hidden="true">&rarr;</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

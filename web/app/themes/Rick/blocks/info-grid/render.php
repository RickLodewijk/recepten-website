<?php
/**
 * Render template for rick/info-grid block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$cards = $attributes['cards'] ?? [];

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-info-grid-section',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <div class="rick-info-grid-block">
            <?php if (!empty($title)) : ?>
                <h2 class="rick-info-grid-title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($subtitle)) : ?>
                <p class="rick-info-grid-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
            <?php endif; ?>

            <?php if (!empty($cards) && is_array($cards)) : ?>
                <div class="rick-info-cards-grid">
                    <?php foreach ($cards as $card) :
                        $card_title = $card['title'] ?? '';
                        $card_desc = $card['description'] ?? '';
                        $card_url = $card['url'] ?? '#';
                    ?>
                        <a href="<?php echo esc_url($card_url); ?>" class="rick-info-card">
                            <div class="rick-info-card__content">
                                <h3 class="rick-info-card__title"><?php echo esc_html($card_title); ?></h3>
                                <?php if (!empty($card_desc)) : ?>
                                    <p class="rick-info-card__desc"><?php echo esc_html($card_desc); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="rick-info-card__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

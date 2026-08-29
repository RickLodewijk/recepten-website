<?php
/**
 * Render template for rick/pepernoot-form block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? 'Nieuwe Pepernoot Toevoegen';
$subtitle = $attributes['subtitle'] ?? 'Vul onderstaand formulier in om direct een nieuwe pepernoot review en beoordeling te publiceren.';
$badge = $attributes['badge'] ?? '🍪 Pepernoten Test & Review';
$button_text = $attributes['buttonText'] ?? '🍪 Pepernoot Opslaan & Publiceren';
$post_status = $attributes['postStatus'] ?? 'publish';

$message = '';
$message_type = '';

// Formulier verwerking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rick_pepernoot_form_nonce'])) {
    if (wp_verify_nonce($_POST['rick_pepernoot_form_nonce'], 'rick_submit_pepernoot')) {
        $name = sanitize_text_field($_POST['pepernoot_title'] ?? '');
        $sub = sanitize_text_field($_POST['pepernoot_subtitle'] ?? '');
        $score = sanitize_text_field($_POST['pepernoot_score'] ?? '');
        $brand = sanitize_text_field($_POST['pepernoot_brand'] ?? '');
        $shop = sanitize_text_field($_POST['pepernoot_shop'] ?? '');
        $price = sanitize_text_field($_POST['pepernoot_price'] ?? '');
        $image = esc_url_raw($_POST['pepernoot_afbeelding'] ?? '');
        $pro = sanitize_text_field($_POST['pepernoot_pro'] ?? '');
        $con = sanitize_text_field($_POST['pepernoot_con'] ?? '');
        $pluspunten = sanitize_textarea_field($_POST['pepernoot_pluspunten'] ?? '');
        $minpunten = sanitize_textarea_field($_POST['pepernoot_minpunten'] ?? '');
        $intro = sanitize_textarea_field($_POST['pepernoot_intro'] ?? '');

        if (!empty($name)) {
            $post_id = wp_insert_post([
                'post_type'    => 'pepernoot',
                'post_title'   => $name,
                'post_content' => $intro,
                'post_status'  => in_array($post_status, ['publish', 'pending', 'draft'], true) ? $post_status : 'publish',
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, 'pepernoot_subtitle', $sub);
                update_post_meta($post_id, 'pepernoot_score', $score);
                update_post_meta($post_id, 'pepernoot_brand', $brand);
                update_post_meta($post_id, 'pepernoot_shop', $shop);
                update_post_meta($post_id, 'pepernoot_price', $price);
                update_post_meta($post_id, 'pepernoot_afbeelding', $image);
                update_post_meta($post_id, 'pepernoot_pro', $pro);
                update_post_meta($post_id, 'pepernoot_con', $con);
                update_post_meta($post_id, 'pepernoot_pluspunten', $pluspunten);
                update_post_meta($post_id, 'pepernoot_minpunten', $minpunten);
                update_post_meta($post_id, 'pepernoot_intro', $intro);

                $permalink = get_permalink($post_id);
                $message = '🎉 Pepernoot "' . esc_html($name) . '" is succesvol toegevoegd! <a href="' . esc_url($permalink) . '" style="text-decoration:underline;color:inherit;font-weight:bold;">Bekijk pepernoot &rarr;</a>';
                $message_type = 'success';
            } else {
                $message = 'Er is een fout opgetreden bij het toevoegen van de pepernoot.';
                $message_type = 'error';
            }
        } else {
            $message = 'Vul minimaal de naam/smaak van de pepernoot in.';
            $message_type = 'error';
        }
    }
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-pepernoot-form-section',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <div class="rick-pepernoot-card">
            <div class="rick-pepernoot-header">
                <?php if (!empty($badge)) : ?>
                    <div class="rick-pepernoot-badge"><?php echo esc_html($badge); ?></div>
                <?php endif; ?>

                <?php if (!empty($title)) : ?>
                    <h2 class="rick-pepernoot-title"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($subtitle)) : ?>
                    <p class="rick-pepernoot-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="rick-form-alert rick-form-alert--<?php echo esc_attr($message_type); ?>">
                    <?php echo wp_kses_post($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="rick-pepernoot-form">
                <?php wp_nonce_field('rick_submit_pepernoot', 'rick_pepernoot_form_nonce'); ?>

                <!-- Sectie 1: Algemeen -->
                <details class="rick-form-collapsible-section" open>
                    <summary class="rick-form-heading rick-form-heading--collapsible">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">🍪</span>
                            <span>1. Basis Informatie</span>
                            <span class="required" style="color: #dc2626; font-weight: bold;">*</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="pepernoot_title">Naam / Smaak Pepernoot <span class="required">*</span></label>
                                <input type="text" id="pepernoot_title" name="pepernoot_title" placeholder="bijv. Van Delft Stroopwafel Pepernoten" required />
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_subtitle">Korte Subtitel</label>
                                <input type="text" id="pepernoot_subtitle" name="pepernoot_subtitle" placeholder="bijv. Knapperige kruidnoten met karamelglazuur" />
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 2: Details & Cijfer -->
                <details class="rick-form-collapsible-section">
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">⭐</span>
                            <span>2. Beoordeling & Specificaties</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--4">
                            <div class="rick-form-group">
                                <label for="pepernoot_score">Score (1-10) ⭐</label>
                                <input type="number" step="0.1" min="1" max="10" id="pepernoot_score" name="pepernoot_score" placeholder="bijv. 8.5" />
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_brand">Merk</label>
                                <input type="text" id="pepernoot_brand" name="pepernoot_brand" placeholder="bijv. Van Delft" />
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_shop">Winkel / Verkooppunt</label>
                                <input type="text" id="pepernoot_shop" name="pepernoot_shop" placeholder="bijv. Albert Heijn" />
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_price">Prijs (€)</label>
                                <input type="text" id="pepernoot_price" name="pepernoot_price" placeholder="bijv. 2,49" />
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 3: Afbeelding -->
                <details class="rick-form-collapsible-section">
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">📸</span>
                            <span>3. Afbeelding</span>
                            <span class="rick-form-heading__optional">(Optioneel)</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-group">
                            <label for="pepernoot_afbeelding">Afbeelding URL</label>
                            <input type="url" id="pepernoot_afbeelding" name="pepernoot_afbeelding" placeholder="https://... of link naar foto van de zak" />
                        </div>
                    </div>
                </details>

                <!-- Sectie 4: Highlights -->
                <details class="rick-form-collapsible-section">
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">👍</span>
                            <span>4. Highlights (Overzicht)</span>
                            <span class="rick-form-heading__optional">(Optioneel)</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="pepernoot_pro">Belangrijkste Pluspunt (Kort)</label>
                                <input type="text" id="pepernoot_pro" name="pepernoot_pro" placeholder="bijv. Echte stroopwafelsmaak en krokante bite" />
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_con">Belangrijkste Minpunt (Kort)</label>
                                <input type="text" id="pepernoot_con" name="pepernoot_con" placeholder="bijv. Iets aan de zoete kant" />
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 5: Uitgebreide Plus- & Minpunten -->
                <details class="rick-form-collapsible-section">
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">📝</span>
                            <span>5. Uitgebreide Plus- & Minpunten</span>
                            <span class="rick-form-heading__optional">(Optioneel)</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="pepernoot_pluspunten">Pluspunten (1 per regel)</label>
                                <textarea id="pepernoot_pluspunten" name="pepernoot_pluspunten" rows="4" placeholder="Krokante structuur&#10;Heerlijke kruidenmix&#10;Hersluitbare zak"></textarea>
                            </div>
                            <div class="rick-form-group">
                                <label for="pepernoot_minpunten">Minpunten (1 per regel)</label>
                                <textarea id="pepernoot_minpunten" name="pepernoot_minpunten" rows="4" placeholder="Iets prijzig&#10;Snel uitverkocht"></textarea>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 6: Beschrijving -->
                <details class="rick-form-collapsible-section">
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">✍️</span>
                            <span>6. Introductie & Review</span>
                            <span class="rick-form-heading__optional">(Optioneel)</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-group">
                            <label for="pepernoot_intro">Introductie / Smaakervaring</label>
                            <textarea id="pepernoot_intro" name="pepernoot_intro" rows="4" placeholder="Schrijf hier de eerste indruk en uitgebreide smaakervaring over deze pepernoot..."></textarea>
                        </div>
                    </div>
                </details>

                <!-- Knop -->
                <div class="rick-form-submit-wrapper">
                    <button type="submit" class="button rick-pepernoot-submit-btn">
                        <span><?php echo esc_html($button_text); ?></span>
                        <span class="rick-pepernoot-submit-arrow" aria-hidden="true">&rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

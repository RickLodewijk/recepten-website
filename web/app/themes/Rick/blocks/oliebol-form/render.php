<?php
/**
 * Render template for rick/oliebol-form block.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block inner content.
 * @var WP_Block $block      Block instance.
 */

$title = $attributes['title'] ?? 'Nieuwe Oliebol Review Toevoegen';
$subtitle = $attributes['subtitle'] ?? 'Deel jouw ervaring! Waar ben je geweest en hoe smaakte de oliebol?';
$badge = $attributes['badge'] ?? '✨ Oliebollen Test & Beoordeling';
$button_text = $attributes['buttonText'] ?? '✨ Oliebol Review Opslaan & Publiceren';
$post_status = $attributes['postStatus'] ?? 'publish';

$message = '';
$message_type = '';

// Formulier verwerking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rick_oliebol_form_nonce'])) {
    if (wp_verify_nonce($_POST['rick_oliebol_form_nonce'], 'rick_submit_oliebol')) {
        $name         = sanitize_text_field($_POST['oliebol_title'] ?? '');
        $locatie      = sanitize_text_field($_POST['oliebol_locatie'] ?? '');
        $auteur       = sanitize_text_field($_POST['oliebol_auteur'] ?? '');
        $smaak        = sanitize_text_field($_POST['oliebol_smaak'] ?? '');
        $cijfer       = sanitize_text_field($_POST['oliebol_cijfer'] ?? '');
        $prijs        = sanitize_text_field($_POST['oliebol_prijs'] ?? '');
        $image        = esc_url_raw($_POST['oliebol_afbeelding'] ?? '');
        $omschrijving = sanitize_textarea_field($_POST['oliebol_omschrijving'] ?? '');

        if (!empty($name)) {
            $post_id = wp_insert_post([
                'post_type'    => 'oliebol',
                'post_title'   => $name,
                'post_content' => $omschrijving,
                'post_status'  => in_array($post_status, ['publish', 'pending', 'draft'], true) ? $post_status : 'publish',
            ]);

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, 'oliebol_locatie', $locatie);
                update_post_meta($post_id, 'oliebol_kraam', $kraam);
                update_post_meta($post_id, 'oliebol_auteur', $auteur);
                update_post_meta($post_id, 'oliebol_smaak', $smaak);
                update_post_meta($post_id, 'oliebol_cijfer', $cijfer);
                update_post_meta($post_id, 'oliebol_prijs', $prijs);
                update_post_meta($post_id, 'oliebol_afbeelding', $image);
                update_post_meta($post_id, 'oliebol_omschrijving', $omschrijving);

                $permalink = get_permalink($post_id);
                $message = '🎉 Oliebol review voor "' . esc_html($name) . '" is succesvol toegevoegd! <a href="' . esc_url($permalink) . '" style="text-decoration:underline;color:inherit;font-weight:bold;">Bekijk review &rarr;</a>';
                $message_type = 'success';
            } else {
                $message = 'Er is een fout opgetreden bij het toevoegen van de oliebol review.';
                $message_type = 'error';
            }
        } else {
            $message = 'Vul minimaal de locatie of naam van de kraam in.';
            $message_type = 'error';
        }
    }
}

$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'rick-oliebol-form-section',
]);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <div class="rick-oliebol-card">
            <div class="rick-oliebol-header">
                <?php if (!empty($badge)) : ?>
                    <div class="rick-oliebol-badge"><?php echo esc_html($badge); ?></div>
                <?php endif; ?>

                <?php if (!empty($title)) : ?>
                    <h2 class="rick-oliebol-title"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>

                <?php if (!empty($subtitle)) : ?>
                    <p class="rick-oliebol-subtitle"><?php echo wp_kses_post($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($message)) : ?>
                <div class="rick-form-alert rick-form-alert--<?php echo esc_attr($message_type); ?>">
                    <?php echo wp_kses_post($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="rick-oliebol-form">
                <?php wp_nonce_field('rick_submit_oliebol', 'rick_oliebol_form_nonce'); ?>

                <!-- Sectie 1: Locatie & Reviewer -->
                <details class="rick-form-collapsible-section" open>
                    <summary class="rick-form-heading rick-form-heading--collapsible">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">📍</span>
                            <span>1. Locatie & Reviewer</span>
                            <span class="required" style="color: #dc2626; font-weight: bold;">*</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="oliebol_title">Locatie / Titel Kraam <span class="required">*</span></label>
                                <input type="text" id="oliebol_title" name="oliebol_title" placeholder="bijv. Oliebollenkraam Spui of Bakkerij Jansen" required />
                            </div>
                            <div class="rick-form-group">
                                <label for="oliebol_locatie">Plaats / Stad</label>
                                <input type="text" id="oliebol_locatie" name="oliebol_locatie" placeholder="bijv. Amsterdam of Utrecht" />
                            </div>
                        </div>

                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="oliebol_auteur">Geschreven door</label>
                                <input type="text" id="oliebol_auteur" name="oliebol_auteur" placeholder="bijv. Rick Lodewijk of jouw naam" value="<?php echo is_user_logged_in() ? esc_attr(wp_get_current_user()->display_name) : ''; ?>" />
                            </div>
                            <div class="rick-form-group">
                                <label for="oliebol_smaak">Smaak / Soort</label>
                                <input type="text" id="oliebol_smaak" name="oliebol_smaak" placeholder="bijv. Naturel, Rozijnen, Nutella, Appel" />
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 2: Beoordeling & Prijs -->
                <details class="rick-form-collapsible-section" open>
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">⭐</span>
                            <span>2. Cijfer & Prijs</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-grid rick-form-grid--2">
                            <div class="rick-form-group">
                                <label for="oliebol_cijfer">Cijfer (0-10) ⭐</label>
                                <input type="number" step="0.1" min="0" max="10" id="oliebol_cijfer" name="oliebol_cijfer" placeholder="bijv. 8.5" />
                            </div>
                            <div class="rick-form-group">
                                <label for="oliebol_prijs">Prijs (€)</label>
                                <input type="text" id="oliebol_prijs" name="oliebol_prijs" placeholder="bijv. 1,50" />
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Sectie 3: Omschrijving & Ervaring -->
                <details class="rick-form-collapsible-section" open>
                    <summary class="rick-form-heading rick-form-heading--collapsible" style="margin-top: 18px;">
                        <div class="rick-form-heading__title">
                            <span class="rick-form-heading__icon">✍️</span>
                            <span>3. Omschrijving & Smaakervaring</span>
                        </div>
                        <span class="rick-form-heading__chevron" aria-hidden="true"></span>
                    </summary>
                    <div class="rick-form-collapsible-body">
                        <div class="rick-form-group">
                            <label for="oliebol_omschrijving">Jouw Oordeel / Review</label>
                            <textarea id="oliebol_omschrijving" name="oliebol_omschrijving" rows="4" placeholder="Hoe was de oliebol? Krokante korst, luchtig deeg, goede vulling, niet te vet?"></textarea>
                        </div>
                        <div class="rick-form-group">
                            <label for="oliebol_afbeelding">Afbeelding URL <span class="rick-form-heading__optional">(Optioneel)</span></label>
                            <input type="url" id="oliebol_afbeelding" name="oliebol_afbeelding" placeholder="https://... of link naar foto van de oliebol" />
                        </div>
                    </div>
                </details>

                <!-- Knop -->
                <div class="rick-form-submit-wrapper">
                    <button type="submit" class="button rick-oliebol-submit-btn">
                        <span><?php echo esc_html($button_text); ?></span>
                        <span class="rick-oliebol-submit-arrow" aria-hidden="true">&rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

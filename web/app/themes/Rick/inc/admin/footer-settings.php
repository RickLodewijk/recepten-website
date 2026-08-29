<?php
/**
 * Footer & Thema Instellingen Optiepagina
 * Ondersteunt zowel ACF Options Page als native WordPress Settings API.
 */

// 1. ACF Options Page registreren indien ACF Pro/Options actief is
if ( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' => 'Footer Instellingen',
        'menu_title' => 'Footer Instellingen',
        'menu_slug'  => 'rick-footer-settings',
        'capability' => 'edit_theme_options',
        'icon_url'   => 'dashicons-layout',
        'position'   => 59,
        'redirect'   => false,
    ));
}

// 2. ACF Velden voor de Footer Optiepagina registreren
if ( function_exists('acf_add_local_field_group') ) {
    acf_add_local_field_group(array(
        'key' => 'group_rick_footer_settings',
        'title' => 'Footer & Thema Instellingen',
        'fields' => array(
            // Tab 1: Merk & Over Ons
            array(
                'key' => 'field_tab_footer_brand',
                'label' => '🏷️ Merk & Teksten',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_brand_name',
                'label' => 'Merk / Website Naam',
                'name' => 'footer_brand_name',
                'type' => 'text',
                'default_value' => 'Rick Recepten',
                'instructions' => 'Naam die linksboven in de footer wordt getoond.',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_brand_icon',
                'label' => 'Merk Icoon (Emoji)',
                'name' => 'footer_brand_icon',
                'type' => 'text',
                'default_value' => '🍳',
                'instructions' => 'Emoji of klein icoon naast de naam.',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_tagline',
                'label' => 'Footer Omschrijving / Tagline',
                'name' => 'footer_tagline',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.',
                'instructions' => 'Korte toelichtende tekst in de eerste footer-kolom.',
            ),
            array(
                'key' => 'field_footer_badge',
                'label' => 'Badge Tekst',
                'name' => 'footer_badge',
                'type' => 'text',
                'default_value' => '👨‍🍳 Huisgemaakt & Verse Recepten',
                'instructions' => 'Badge onderaan de eerste kolom.',
            ),

            // Tab 2: Bakker Tip
            array(
                'key' => 'field_tab_footer_tip',
                'label' => '💡 Tip van de Bakker',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_tip_heading',
                'label' => 'Tip Koptekst',
                'name' => 'footer_tip_heading',
                'type' => 'text',
                'default_value' => '💡 Baktip',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_tip_text',
                'label' => 'Tip Inhoud',
                'name' => 'footer_tip_text',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!',
                'instructions' => 'Korte tip of quote in de rechter footer-kolom.',
            ),

            // Tab 3: Copyright & Socials
            array(
                'key' => 'field_tab_footer_copyright',
                'label' => '©️ Copyright & Socials',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_copyright',
                'label' => 'Copyright Tekst',
                'name' => 'footer_copyright',
                'type' => 'text',
                'default_value' => '© {year} Rick Recepten. Alle rechten voorbehouden.',
                'instructions' => 'Gebruik {year} voor het huidige jaartal.',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_credit',
                'label' => 'Credit / Subregel',
                'name' => 'footer_credit',
                'type' => 'text',
                'default_value' => 'Gemaakt voor kook- en bakliefhebbers 🥐',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_email',
                'label' => 'Contact E-mailadres (Optioneel)',
                'name' => 'footer_email',
                'type' => 'email',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_instagram',
                'label' => 'Instagram URL (Optioneel)',
                'name' => 'footer_instagram',
                'type' => 'url',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'rick-footer-settings',
                ),
            ),
        ),
    ));
}

// 3. Native WordPress Admin Menu Fallback (als ACF Pro options page niet aanwezig is)
add_action('admin_menu', 'rick_register_footer_admin_menu');
function rick_register_footer_admin_menu() {
    // Alleen registreren als ACF Options page niet al actief is
    if ( ! function_exists('acf_add_options_page') ) {
        add_theme_page(
            'Footer Instellingen',
            'Footer Instellingen',
            'edit_theme_options',
            'rick-footer-settings',
            'rick_render_footer_admin_page'
        );
    }
}

add_action('admin_init', 'rick_register_footer_settings');
function rick_register_footer_settings() {
    register_setting('rick_footer_options_group', 'rick_footer_settings', array(
        'sanitize_callback' => 'rick_sanitize_footer_settings',
        'default' => array(),
    ));
}

function rick_sanitize_footer_settings($input) {
    $sanitized = array();
    if (is_array($input)) {
        foreach ($input as $key => $val) {
            $sanitized[$key] = sanitize_text_field($val);
        }
    }
    return $sanitized;
}

function rick_render_footer_admin_page() {
    $settings = get_option('rick_footer_settings', array());
    ?>
    <div class="wrap" style="max-width: 860px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <h1 style="display: flex; align-items: center; gap: 10px; color: #92400e;">
            <span class="dashicons dashicons-layout" style="font-size: 28px; width: 28px; height: 28px;"></span>
            Footer & Thema Instellingen
        </h1>
        <p style="color: #6b7280; font-size: 14px;">Pas hier de teksten, tips en copyrightvermelding aan die onderaan elke pagina in de footer verschijnen.</p>

        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>De footer instellingen zijn succesvol opgeslagen!</strong></p>
            </div>
        <?php endif; ?>

        <form method="post" action="options.php" style="background: #ffffff; padding: 24px; border: 1px solid #c3c4c7; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 15px;">
            <?php settings_fields('rick_footer_options_group'); ?>

            <h2 style="color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 8px; margin-top: 0;">🏷️ Merk & Tagline</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="footer_brand_name">Merk / Website Naam</label></th>
                    <td><input type="text" id="footer_brand_name" name="rick_footer_settings[footer_brand_name]" value="<?php echo esc_attr($settings['footer_brand_name'] ?? 'Rick Recepten'); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="footer_brand_icon">Merk Icoon (Emoji)</label></th>
                    <td><input type="text" id="footer_brand_icon" name="rick_footer_settings[footer_brand_icon]" value="<?php echo esc_attr($settings['footer_brand_icon'] ?? '🍳'); ?>" style="width: 80px;" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="footer_tagline">Omschrijving / Tagline</label></th>
                    <td><textarea id="footer_tagline" name="rick_footer_settings[footer_tagline]" rows="3" class="large-text"><?php echo esc_textarea($settings['footer_tagline'] ?? 'De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.'); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="footer_badge">Badge Tekst</label></th>
                    <td><input type="text" id="footer_badge" name="rick_footer_settings[footer_badge]" value="<?php echo esc_attr($settings['footer_badge'] ?? '👨‍🍳 Huisgemaakt & Verse Recepten'); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2 style="color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 8px; margin-top: 25px;">💡 Tip van de Bakker</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="footer_tip_heading">Koptekst</label></th>
                    <td><input type="text" id="footer_tip_heading" name="rick_footer_settings[footer_tip_heading]" value="<?php echo esc_attr($settings['footer_tip_heading'] ?? '💡 Baktip'); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="footer_tip_text">Baktip Inhoud</label></th>
                    <td><textarea id="footer_tip_text" name="rick_footer_settings[footer_tip_text]" rows="3" class="large-text"><?php echo esc_textarea($settings['footer_tip_text'] ?? 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!'); ?></textarea></td>
                </tr>
            </table>

            <h2 style="color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 8px; margin-top: 25px;">©️ Copyright & Credits</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="footer_copyright">Copyright Tekst</label></th>
                    <td>
                        <input type="text" id="footer_copyright" name="rick_footer_settings[footer_copyright]" value="<?php echo esc_attr($settings['footer_copyright'] ?? '© {year} Rick Recepten. Alle rechten voorbehouden.'); ?>" class="regular-text" />
                        <p class="description">Gebruik <code>{year}</code> om automatisch het huidige jaartal in te voegen.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="footer_credit">Credit / Subtekst</label></th>
                    <td><input type="text" id="footer_credit" name="rick_footer_settings[footer_credit]" value="<?php echo esc_attr($settings['footer_credit'] ?? 'Gemaakt voor kook- en bakliefhebbers 🥐'); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <?php submit_button('Instellingen Opslaan', 'primary', 'submit', true, array('style' => 'background: #d97706; border-color: #b45309; padding: 6px 20px; font-weight: 700;')); ?>
        </form>
    </div>
    <?php
}

/**
 * Centrale helper om een footer instelling op te halen.
 */
function rick_get_footer_setting( $key, $default = '' ) {
    // 1. Check ACF Options veld indien ACF actief is
    if ( function_exists('get_field') ) {
        $acf_val = get_field( $key, 'option' );
        if ( ! empty( $acf_val ) ) {
            return $acf_val;
        }
    }

    // 2. Check native settings array
    $settings = get_option( 'rick_footer_settings', array() );
    if ( is_array( $settings ) && ! empty( $settings[ $key ] ) ) {
        return $settings[ $key ];
    }

    // 3. Check losse optie
    $single_opt = get_option( 'rick_footer_' . $key );
    if ( ! empty( $single_opt ) ) {
        return $single_opt;
    }

    return $default;
}

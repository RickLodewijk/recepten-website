<?php
/**
 * Algemene Thema Instellingen (Header, Navigatie & Footer)
 * 
 * Ondersteunt zowel ACF Options Page als native WordPress Settings API
 * voor 100% compatibiliteit met InfinityFree (zonder afhankelijkheid van ACF Pro).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. ACF Options Page registreren indien ACF Pro actief is
if ( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title' => 'Algemene Instellingen',
        'menu_title' => 'Algemene Instellingen',
        'menu_slug'  => 'rick-general-settings',
        'capability' => 'edit_theme_options',
        'icon_url'   => 'dashicons-admin-generic',
        'position'   => 59,
        'redirect'   => false,
    ));
}

// 2. ACF Velden voor Algemene Instellingen registreren
if ( function_exists('acf_add_local_field_group') ) {
    acf_add_local_field_group(array(
        'key' => 'group_rick_general_settings',
        'title' => 'Algemene Thema Instellingen',
        'fields' => array(
            // ================== TAB 1: HEADER & NAVIGATIE ==================
            array(
                'key' => 'field_tab_general_header',
                'label' => '🎨 Header & Navigatie',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_header_style',
                'label' => 'Header Kleurthema (CSS Class)',
                'name' => 'header_style',
                'type' => 'select',
                'instructions' => 'Kies het kleurthema voor de zwevende capsule header (wordt als class aan de header toegevoegd).',
                'choices' => array(
                    'header-brown' => 'Bruin / Warm Amber (Bakkerij stijl - class: header-brown)',
                    'header-blue'  => 'Blauw (Modern Helderblauw uit voorbeeld - class: header-blue)',
                ),
                'default_value' => 'header-brown',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_header_cta_enable',
                'label' => 'Actieknop (CTA) Tonen',
                'name' => 'header_cta_enable',
                'type' => 'true_false',
                'message' => 'Toon de opvallende actieknop rechts in de header capsule',
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_header_cta_text',
                'label' => 'Actieknop Tekst',
                'name' => 'header_cta_text',
                'type' => 'text',
                'default_value' => '🍪 Pepernoot Beoordelen',
                'instructions' => 'Tekst die in de knop verschijnt (inclusief eventuele emoji).',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_header_cta_url',
                'label' => 'Actieknop Link (URL)',
                'name' => 'header_cta_url',
                'type' => 'text',
                'default_value' => '/pepernoot-registreren/',
                'instructions' => 'Relatieve of volledige URL waar de knop naar verwijst.',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_header_brand_name',
                'label' => 'Website / Merk Naam',
                'name' => 'header_brand_name',
                'type' => 'text',
                'default_value' => 'Rick Recepten',
                'instructions' => 'Naam die links in de capsule header getoond wordt.',
                'wrapper' => array('width' => '50'),
            ),
        
            // ================== TAB 2: FOOTER MERK & OVER ONS ==================
            array(
                'key' => 'field_tab_footer_brand',
                'label' => '🏷️ Footer Merk & Teksten',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_brand_name',
                'label' => 'Footer Merknaam',
                'name' => 'footer_brand_name',
                'type' => 'text',
                'default_value' => 'Rick Recepten',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_brand_icon',
                'label' => 'Footer Icoon (Emoji)',
                'name' => 'footer_brand_icon',
                'type' => 'text',
                'default_value' => '🍳',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_tagline',
                'label' => 'Footer Tagline',
                'name' => 'footer_tagline',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => 'De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.',
            ),
            array(
                'key' => 'field_footer_badge',
                'label' => 'Footer Badge Tekst',
                'name' => 'footer_badge',
                'type' => 'text',
                'default_value' => '👨‍🍳 Huisgemaakt & Verse Recepten',
            ),

            // ================== TAB 3: BAKKER TIP ==================
            array(
                'key' => 'field_tab_footer_tip',
                'label' => '💡 Tip van de Bakker',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_tip_heading',
                'label' => 'Koptekst',
                'name' => 'footer_tip_heading',
                'type' => 'text',
                'default_value' => '💡 Baktip van de dag',
            ),
            array(
                'key' => 'field_footer_tip_text',
                'label' => 'Baktip Inhoud',
                'name' => 'footer_tip_text',
                'type' => 'textarea',
                'rows' => 4,
                'default_value' => 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!',
            ),

            // ================== TAB 4: COPYRIGHT & CONTACT ==================
            array(
                'key' => 'field_tab_footer_copyright',
                'label' => '©️ Copyright & Contact',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_copyright',
                'label' => 'Copyright Tekst',
                'name' => 'footer_copyright',
                'type' => 'text',
                'default_value' => '© {year} Rick Recepten. Alle rechten voorbehouden.',
                'instructions' => 'Gebruik {year} om automatisch het huidige jaartal in te voegen.',
            ),
            array(
                'key' => 'field_footer_credit',
                'label' => 'Credits / Subtekst',
                'name' => 'footer_credit',
                'type' => 'text',
                'default_value' => 'Gemaakt voor kook- en bakliefhebbers 🥐',
            ),
            array(
                'key' => 'field_footer_email',
                'label' => 'Contact E-mailadres (optioneel)',
                'name' => 'footer_email',
                'type' => 'email',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_footer_instagram',
                'label' => 'Instagram URL (optioneel)',
                'name' => 'footer_instagram',
                'type' => 'url',
                'wrapper' => array('width' => '50'),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'rick-general-settings',
                ),
            ),
        ),
    ));
}

// 3. Native WordPress Settings API fallback (werkt zonder ACF Pro op elk type hosting)
add_action('admin_menu', 'rick_register_general_settings_menu');
function rick_register_general_settings_menu() {
    // Alleen registreren als ACF Options page niet al getoond wordt
    if ( ! function_exists('acf_add_options_page') ) {
        add_menu_page(
            'Algemene Instellingen',
            '⚙️ Algemene Instellingen',
            'edit_theme_options',
            'rick-general-settings',
            'rick_render_general_admin_page',
            'dashicons-admin-generic',
            59
        );
    }
}

add_action('admin_init', 'rick_register_general_settings_fields');
function rick_register_general_settings_fields() {
    register_setting('rick_general_options_group', 'rick_general_settings', array(
        'sanitize_callback' => 'rick_sanitize_general_settings',
    ));
}

function rick_sanitize_general_settings($input) {
    $sanitized = array();
    if (is_array($input)) {
        foreach ($input as $key => $val) {
            if (in_array($key, array('footer_tagline', 'footer_tip_text'), true)) {
                $sanitized[$key] = sanitize_textarea_field($val);
            } elseif ($key === 'header_cta_enable') {
                $sanitized[$key] = ! empty($val) ? '1' : '0';
            } elseif (in_array($key, array('header_cta_url', 'footer_instagram'), true)) {
                $sanitized[$key] = esc_url_raw(trim($val));
            } elseif ($key === 'footer_email') {
                $sanitized[$key] = sanitize_email($val);
            } else {
                $sanitized[$key] = sanitize_text_field($val);
            }
        }
    }
    return $sanitized;
}

function rick_render_general_admin_page() {
    $settings = get_option('rick_general_settings', array());
    // Migratie fallback van oude footer_settings
    if (empty($settings)) {
        $settings = get_option('rick_footer_settings', array());
    }

    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'header';
    ?>
    <div class="wrap" style="max-width: 920px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <h1 style="display: flex; align-items: center; gap: 10px; color: #1e293b; margin-bottom: 8px;">
            <span class="dashicons dashicons-admin-generic" style="font-size: 30px; width: 30px; height: 30px; color: #d97706;"></span>
            Algemene Thema Instellingen
        </h1>
        <p style="color: #64748b; font-size: 14px; margin-top: 0;">Beheer hier de zwevende capsule header, de navigatie-actieknop en de footer-teksten.</p>

        <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
            <div class="notice notice-success is-dismissible" style="padding: 10px 14px;">
                <p><strong>🎉 De instellingen zijn succesvol opgeslagen!</strong></p>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigatie -->
        <h2 class="nav-tab-wrapper" style="margin-bottom: 20px;">
            <a href="?page=rick-general-settings&tab=header" class="nav-tab <?php echo $current_tab === 'header' ? 'nav-tab-active' : ''; ?>">
                🎨 Header & Navigatie
            </a>
            <a href="?page=rick-general-settings&tab=footer" class="nav-tab <?php echo $current_tab === 'footer' ? 'nav-tab-active' : ''; ?>">
                🏷️ Footer & Teksten
            </a>
        </h2>

        <form method="post" action="options.php" style="background: #ffffff; padding: 28px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <?php settings_fields('rick_general_options_group'); ?>

            <?php if ($current_tab === 'header') : ?>
                <h2 style="color: #0f172a; border-bottom: 2px solid #fef3c7; padding-bottom: 10px; margin-top: 0; display:flex; align-items:center; gap:8px;">
                    <span>🎨 Zwevende Capsule Header & Actieknop</span>
                </h2>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="header_style"><strong>Header Kleurthema</strong></label>
                        </th>
                        <td>
                            <?php $style = $settings['header_style'] ?? 'header-brown'; ?>
                            <fieldset>
                                <label style="display:block; margin-bottom: 8px; font-weight: 600; cursor: pointer;">
                                    <input type="radio" name="rick_general_settings[header_style]" value="header-brown" <?php checked($style, 'header-brown'); ?>>
                                    <span style="display:inline-block; width: 14px; height: 14px; background:#d97706; border-radius: 50%; vertical-align: middle; margin: 0 6px;"></span>
                                    Bruin / Warm Amber (Bakkerij stijl &mdash; <code>header-brown</code>)
                                </label>
                                <label style="display:block; font-weight: 600; cursor: pointer;">
                                    <input type="radio" name="rick_general_settings[header_style]" value="header-blue" <?php checked($style, 'header-blue'); ?>>
                                    <span style="display:inline-block; width: 14px; height: 14px; background:#2563eb; border-radius: 50%; vertical-align: middle; margin: 0 6px;"></span>
                                    Blauw (Modern Helderblauw uit voorbeeld &mdash; <code>header-blue</code>)
                                </label>
                            </fieldset>
                            <p class="description" style="margin-top: 6px;">Deze class wordt aan het <code>&lt;header&gt;</code> element toegevoegd. De stijlen kun je later naar wens aanpassen in <code>style.css</code>.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <strong>Actieknop Tonen</strong>
                        </th>
                        <td>
                            <label style="display:inline-flex; align-items:center; gap:8px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="rick_general_settings[header_cta_enable]" value="1" <?php checked($settings['header_cta_enable'] ?? '1', '1'); ?> />
                                <span>Toon de opvallende actieknop rechts in de header</span>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="header_cta_text"><strong>Actieknop Tekst</strong></label>
                        </th>
                        <td>
                            <input type="text" id="header_cta_text" name="rick_general_settings[header_cta_text]" value="<?php echo esc_attr($settings['header_cta_text'] ?? '🍪 Pepernoot Beoordelen'); ?>" class="regular-text" style="padding: 6px 10px;" />
                            <p class="description">De tekst op de knop rechts in de capsule.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="header_cta_url"><strong>Actieknop Link (URL)</strong></label>
                        </th>
                        <td>
                            <input type="text" id="header_cta_url" name="rick_general_settings[header_cta_url]" value="<?php echo esc_attr($settings['header_cta_url'] ?? '/pepernoot-registreren/'); ?>" class="regular-text" style="padding: 6px 10px;" />
                            <p class="description">Bijvoorbeeld <code>/pepernoot-registreren/</code> of een volledige link.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="header_brand_name"><strong>Merknaam (Links)</strong></label>
                        </th>
                        <td>
                            <input type="text" id="header_brand_name" name="rick_general_settings[header_brand_name]" value="<?php echo esc_attr($settings['header_brand_name'] ?? 'Rick Recepten'); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="header_brand_icon"><strong>Merk Icoon (Emoji)</strong></label>
                        </th>
                        <td>
                            <input type="text" id="header_brand_icon" name="rick_general_settings[header_brand_icon]" value="<?php echo esc_attr($settings['header_brand_icon'] ?? '👨‍🍳'); ?>" style="width: 80px; text-align: center; font-size: 16px;" />
                            <p class="description">Emoji of klein teken naast de sitenaam.</p>
                        </td>
                    </tr>
                </table>

                <!-- Onzichtbare velden voor footer behouden als je op header tab opslaat -->
                <?php foreach ($settings as $k => $v) : 
                    if (strpos($k, 'footer_') === 0) : ?>
                        <input type="hidden" name="rick_general_settings[<?php echo esc_attr($k); ?>]" value="<?php echo esc_attr($v); ?>" />
                    <?php endif; 
                endforeach; ?>

            <?php else : ?>
                <!-- FOOTER TAB -->
                <h2 style="color: #0f172a; border-bottom: 2px solid #fef3c7; padding-bottom: 10px; margin-top: 0;">🏷️ Footer Merk & Tagline</h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="footer_brand_name">Merk / Website Naam</label></th>
                        <td><input type="text" id="footer_brand_name" name="rick_general_settings[footer_brand_name]" value="<?php echo esc_attr($settings['footer_brand_name'] ?? 'Rick Recepten'); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_brand_icon">Merk Icoon (Emoji)</label></th>
                        <td><input type="text" id="footer_brand_icon" name="rick_general_settings[footer_brand_icon]" value="<?php echo esc_attr($settings['footer_brand_icon'] ?? '🍳'); ?>" style="width: 80px; text-align: center;" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_tagline">Omschrijving / Tagline</label></th>
                        <td><textarea id="footer_tagline" name="rick_general_settings[footer_tagline]" rows="3" class="large-text"><?php echo esc_textarea($settings['footer_tagline'] ?? 'De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.'); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_badge">Badge Tekst</label></th>
                        <td><input type="text" id="footer_badge" name="rick_general_settings[footer_badge]" value="<?php echo esc_attr($settings['footer_badge'] ?? '👨‍🍳 Huisgemaakt & Verse Recepten'); ?>" class="regular-text" /></td>
                    </tr>
                </table>

                <h2 style="color: #0f172a; border-bottom: 2px solid #fef3c7; padding-bottom: 10px; margin-top: 25px;">💡 Tip van de Bakker</h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="footer_tip_heading">Koptekst</label></th>
                        <td><input type="text" id="footer_tip_heading" name="rick_general_settings[footer_tip_heading]" value="<?php echo esc_attr($settings['footer_tip_heading'] ?? '💡 Baktip van de dag'); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_tip_text">Baktip Inhoud</label></th>
                        <td><textarea id="footer_tip_text" name="rick_general_settings[footer_tip_text]" rows="3" class="large-text"><?php echo esc_textarea($settings['footer_tip_text'] ?? 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!'); ?></textarea></td>
                    </tr>
                </table>

                <h2 style="color: #0f172a; border-bottom: 2px solid #fef3c7; padding-bottom: 10px; margin-top: 25px;">©️ Copyright & Credits</h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="footer_copyright">Copyright Tekst</label></th>
                        <td>
                            <input type="text" id="footer_copyright" name="rick_general_settings[footer_copyright]" value="<?php echo esc_attr($settings['footer_copyright'] ?? '© {year} Rick Recepten. Alle rechten voorbehouden.'); ?>" class="regular-text" />
                            <p class="description">Gebruik <code>{year}</code> om automatisch het huidige jaartal in te voegen.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_credit">Credit / Subtekst</label></th>
                        <td><input type="text" id="footer_credit" name="rick_general_settings[footer_credit]" value="<?php echo esc_attr($settings['footer_credit'] ?? 'Gemaakt voor kook- en bakliefhebbers 🥐'); ?>" class="regular-text" /></td>
                    </tr>
                </table>

                <!-- Onzichtbare velden voor header behouden als je op footer tab opslaat -->
                <?php foreach ($settings as $k => $v) : 
                    if (strpos($k, 'header_') === 0) : ?>
                        <input type="hidden" name="rick_general_settings[<?php echo esc_attr($k); ?>]" value="<?php echo esc_attr($v); ?>" />
                    <?php endif; 
                endforeach; ?>
            <?php endif; ?>

            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                <?php submit_button('Instellingen Opslaan', 'primary', 'submit', false, array('style' => 'background: #d97706; border-color: #b45309; padding: 6px 24px; font-weight: 700; border-radius: 6px;')); ?>
            </div>
        </form>
    </div>
    <?php
}

// 4. Centrale getters voor Header & Footer instellingen
function rick_get_general_setting( $key, $default = '' ) {
    if ( function_exists('get_field') ) {
        $val = get_field( $key, 'option' );
        if ( $val !== null && $val !== '' ) {
            return $val;
        }
    }

    $settings = get_option('rick_general_settings', array());
    if ( isset($settings[$key]) && $settings[$key] !== '' ) {
        return $settings[$key];
    }

    // Fallback naar oude footer optietabel indien van toepassing
    $footer_settings = get_option('rick_footer_settings', array());
    if ( isset($footer_settings[$key]) && $footer_settings[$key] !== '' ) {
        return $footer_settings[$key];
    }

    return $default;
}

function rick_get_header_setting( $key, $default = '' ) {
    return rick_get_general_setting( $key, $default );
}

function rick_get_footer_setting( $key, $default = '' ) {
    return rick_get_general_setting( $key, $default );
}

<?php
/**
 * Recepten Scraper & Import Tool voor WordPress (InfinityFree compatibel)
 * 
 * Haalt receptgegevens op via Schema.org Recipe JSON-LD data en zet
 * ingrediënten om naar het formaat: "Naam | Hoeveelheid".
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Registreer admin menu onder Recepten
add_action( 'admin_menu', 'rick_register_recept_scraper_menu' );
function rick_register_recept_scraper_menu() {
    add_submenu_page(
        'edit.php?post_type=recept',
        'Recept Importeren',
        '📥 Recept Importeren',
        'edit_posts',
        'recept-importeren',
        'rick_render_recept_scraper_page'
    );
}

// 2. Extra kolommen in het receptenoverzicht (Zelf gemaakt + Bron)
add_filter( 'manage_recept_posts_columns', 'rick_recept_custom_columns' );
function rick_recept_custom_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $title ) {
        $new_columns[ $key ] = $title;
        if ( $key === 'title' ) {
            $new_columns['is_zelf_gemaakt'] = 'Zelf gemaakt?';
            $new_columns['recept_bron'] = 'Bron';
        }
    }
    return $new_columns;
}

add_action( 'manage_recept_posts_custom_column', 'rick_recept_custom_column_content', 10, 2 );
function rick_recept_custom_column_content( $column, $post_id ) {
    if ( $column === 'is_zelf_gemaakt' ) {
        $is_gemaakt = get_post_meta( $post_id, 'is_zelf_gemaakt', true );
        $toggle_url = wp_nonce_url(
            admin_url( 'admin-post.php?action=rick_toggle_zelf_gemaakt&post_id=' . $post_id ),
            'rick_toggle_zelf_gemaakt_' . $post_id
        );

        if ( $is_gemaakt == '1' ) {
            echo '<a href="' . esc_url( $toggle_url ) . '" title="Klik om te wijzigen naar Nog niet gemaakt" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;font-weight:600;color:#15803d;background:#dcfce7;padding:3px 8px;border-radius:12px;font-size:12px;">';
            echo '✅ Ja, gemaakt';
            echo '</a>';
        } else {
            echo '<a href="' . esc_url( $toggle_url ) . '" title="Klik om te wijzigen naar Zelf gemaakt" style="display:inline-flex;align-items:center;gap:4px;text-decoration:none;font-weight:600;color:#854d0e;background:#fef9c3;padding:3px 8px;border-radius:12px;font-size:12px;">';
            echo '⏳ Nog niet';
            echo '</a>';
        }
    }

    if ( $column === 'recept_bron' ) {
        $bron_naam = get_post_meta( $post_id, 'bron_naam', true );
        $bron_url = get_post_meta( $post_id, 'bron_url', true );
        $is_auto = get_post_meta( $post_id, 'is_geautomatiseerd', true );

        if ( ! empty( $bron_url ) ) {
            $label = ! empty( $bron_naam ) ? $bron_naam : 'Link';
            echo '<a href="' . esc_url( $bron_url ) . '" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">🌐 ' . esc_html( $label ) . '</a>';
        } elseif ( ! empty( $bron_naam ) ) {
            echo esc_html( $bron_naam );
        } elseif ( $is_auto == '1' ) {
            echo '<span style="color:#6b7280;font-size:12px;">Geïmporteerd</span>';
        } else {
            echo '<span style="color:#9ca3af;font-size:12px;">Eigen recept</span>';
        }
    }
}

// Snelle actie om zelf-gemaakt status met één klik om te schakelen
add_action( 'admin_post_rick_toggle_zelf_gemaakt', 'rick_handle_toggle_zelf_gemaakt' );
function rick_handle_toggle_zelf_gemaakt() {
    $post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
    check_admin_referer( 'rick_toggle_zelf_gemaakt_' . $post_id );

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( 'Geen toestemming.' );
    }

    $current = get_post_meta( $post_id, 'is_zelf_gemaakt', true );
    $new = ( $current == '1' ) ? '0' : '1';

    if ( function_exists( 'update_field' ) ) {
        update_field( 'is_zelf_gemaakt', ( $new == '1' ? true : false ), $post_id );
    }
    update_post_meta( $post_id, 'is_zelf_gemaakt', $new );

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=recept' ) );
    exit;
}

// 2b. Bulk Actions toevoegen aan de bulkacties-dropdown
add_filter( 'bulk_actions-edit-recept', 'rick_register_recept_bulk_actions' );
function rick_register_recept_bulk_actions( $bulk_actions ) {
    $bulk_actions['mark_zelf_gemaakt'] = '👨‍🍳 Markeer als: Zelf gemaakt';
    $bulk_actions['mark_niet_zelf_gemaakt'] = '⏳ Markeer als: Nog niet gemaakt';
    return $bulk_actions;
}

// Bulk Actions afhandelen
add_filter( 'handle_bulk_actions-edit-recept', 'rick_handle_recept_bulk_actions', 10, 3 );
function rick_handle_recept_bulk_actions( $redirect_to, $doaction, $post_ids ) {
    if ( ! in_array( $doaction, array( 'mark_zelf_gemaakt', 'mark_niet_zelf_gemaakt' ), true ) ) {
        return $redirect_to;
    }

    $new_val = ( $doaction === 'mark_zelf_gemaakt' ) ? '1' : '0';
    $count = 0;

    foreach ( $post_ids as $post_id ) {
        if ( current_user_can( 'edit_post', $post_id ) ) {
            if ( function_exists( 'update_field' ) ) {
                update_field( 'is_zelf_gemaakt', ( $new_val === '1' ), $post_id );
            }
            update_post_meta( $post_id, 'is_zelf_gemaakt', $new_val );
            $count++;
        }
    }

    $redirect_to = add_query_arg( array(
        'bulk_zelf_gemaakt_count' => $count,
        'bulk_zelf_gemaakt_action' => $doaction,
    ), $redirect_to );

    return $redirect_to;
}

// Melding tonen na uitvoeren bulkactie
add_action( 'admin_notices', 'rick_recept_bulk_action_admin_notice' );
function rick_recept_bulk_action_admin_notice() {
    global $pagenow;
    if ( $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'recept' && ! empty( $_GET['bulk_zelf_gemaakt_count'] ) ) {
        $count = (int) $_GET['bulk_zelf_gemaakt_count'];
        $action = isset( $_GET['bulk_zelf_gemaakt_action'] ) ? sanitize_text_field( $_GET['bulk_zelf_gemaakt_action'] ) : '';
        $label = ( $action === 'mark_zelf_gemaakt' ) ? 'gemarkeerd als: Zelf gemaakt' : 'gemarkeerd als: Nog niet gemaakt';

        echo '<div class="notice notice-success is-dismissible" style="padding:10px 14px;">';
        echo '<p style="margin:0;font-size:14px;">🎉 <strong>' . sprintf( '%d recept(en) succesvol %s.', $count, $label ) . '</strong></p>';
        echo '</div>';
    }
}

// 2c. Filter dropdown toevoegen aan de lijst (boven de tabel)
add_action( 'restrict_manage_posts', 'rick_recept_table_filter_dropdown' );
function rick_recept_table_filter_dropdown( $post_type ) {
    if ( $post_type !== 'recept' ) {
        return;
    }

    $current_filter = isset( $_GET['filter_zelf_gemaakt'] ) ? sanitize_text_field( $_GET['filter_zelf_gemaakt'] ) : '';
    ?>
    <select name="filter_zelf_gemaakt" id="filter_zelf_gemaakt">
        <option value="">Alle recepten (Zelf gemaakt & niet)</option>
        <option value="ja" <?php selected( $current_filter, 'ja' ); ?>>✅ Alleen zelf gemaakt</option>
        <option value="nee" <?php selected( $current_filter, 'nee' ); ?>>⏳ Alleen nog niet gemaakt</option>
    </select>
    <?php
}

add_filter( 'parse_query', 'rick_recept_table_filter_query' );
function rick_recept_table_filter_query( $query ) {
    global $pagenow;
    $is_admin_recept_list = is_admin() && $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'recept';

    if ( ! $is_admin_recept_list || ! isset( $_GET['filter_zelf_gemaakt'] ) || $_GET['filter_zelf_gemaakt'] === '' ) {
        return;
    }

    $filter = sanitize_text_field( $_GET['filter_zelf_gemaakt'] );
    $meta_query = $query->get( 'meta_query' );
    if ( ! is_array( $meta_query ) ) {
        $meta_query = array();
    }

    if ( $filter === 'ja' ) {
        $meta_query[] = array(
            'key'     => 'is_zelf_gemaakt',
            'value'   => '1',
            'compare' => '=',
        );
    } elseif ( $filter === 'nee' ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => 'is_zelf_gemaakt',
                'value'   => '1',
                'compare' => '!=',
            ),
            array(
                'key'     => 'is_zelf_gemaakt',
                'compare' => 'NOT EXISTS',
            ),
        );
    }

    $query->set( 'meta_query', $meta_query );
}

// 3. Render de Scraper Admin Pagina
function rick_render_recept_scraper_page() {
    $message = '';
    $error = '';
    $created_post_id = 0;

    if ( isset( $_POST['rick_scrape_nonce'] ) && wp_verify_nonce( $_POST['rick_scrape_nonce'], 'rick_scrape_action' ) ) {
        $url = isset( $_POST['recipe_url'] ) ? esc_url_raw( trim( $_POST['recipe_url'] ) ) : '';
        $post_status = isset( $_POST['post_status'] ) && in_array( $_POST['post_status'], array( 'publish', 'draft' ) ) ? $_POST['post_status'] : 'draft';
        $is_zelf_gemaakt = ! empty( $_POST['is_zelf_gemaakt'] ) ? 1 : 0;
        $category_id = ! empty( $_POST['recept_categorie'] ) ? (int) $_POST['recept_categorie'] : 0;

        if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
            $error = 'Voer een geldige URL in.';
        } else {
            $result = rick_import_recipe_from_url( $url, $post_status, $is_zelf_gemaakt, $category_id );
            if ( is_wp_error( $result ) ) {
                $error = $result->get_error_message();
            } else {
                $created_post_id = $result;
                $message = 'Het recept is succesvol binnengehaald!';
            }
        }
    }

    $categories = get_terms( array(
        'taxonomy'   => 'recept_categorie',
        'hide_empty' => false,
    ) );
    ?>
    <div class="wrap" style="max-width: 860px;">
        <h1 style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
            <span>📥 Recept Importeren via Link</span>
        </h1>

        <?php if ( ! empty( $error ) ) : ?>
            <div class="notice notice-error is-dismissible">
                <p><strong>Fout:</strong> <?php echo esc_html( $error ); ?></p>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $message ) && $created_post_id ) : ?>
            <div class="notice notice-success is-dismissible" style="padding:12px 15px;">
                <p style="font-size:15px;margin:0 0 10px 0;"><strong>🎉 <?php echo esc_html( $message ); ?></strong></p>
                <div style="display:flex;gap:10px;">
                    <a href="<?php echo esc_url( get_edit_post_link( $created_post_id ) ); ?>" class="button button-primary">
                        ✏️ Recept bekijken / bewerken
                    </a>
                    <a href="<?php echo esc_url( get_permalink( $created_post_id ) ); ?>" target="_blank" class="button">
                        👁️ Bekijk op de website
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="postbox" style="padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <p style="margin-top:0;font-size:14px;color:#4b5563;">
                Plak hieronder de link van een receptenwebsite (zoals <strong>Allerhande (AH)</strong>, <strong>Lekker en Simpel</strong>, <strong>24Kitchen</strong>, <strong>Rutger Bakt</strong>, <strong>Smulweb</strong>, etc.). 
                De scraper leest de titel, afbeelding, bereidingstijd, ingrediënten en bereidingswijze automatisch uit en zet deze direct in jouw receptenformaat.
            </p>

            <form method="post" action="">
                <?php wp_nonce_field( 'rick_scrape_action', 'rick_scrape_nonce' ); ?>

                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="recipe_url"><strong>Link naar recept (URL)</strong> <span style="color:red;">*</span></label>
                            </th>
                            <td>
                                <input name="recipe_url" type="url" id="recipe_url" placeholder="https://www.ah.nl/allerhande/recept/..." class="large-text" required autofocus style="padding:8px 12px;font-size:14px;" />
                                <p class="description">Plak de volledige URL van de webpagina.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="post_status"><strong>Status na import</strong></label>
                            </th>
                            <td>
                                <select name="post_status" id="post_status" style="padding:4px 8px;">
                                    <option value="draft">Concept (Aanbevolen - eerst even nakijken)</option>
                                    <option value="publish">Direct publiceren</option>
                                </select>
                            </td>
                        </tr>

                        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
                            <tr>
                                <th scope="row">
                                    <label for="recept_categorie"><strong>Categorie (optioneel)</strong></label>
                                </th>
                                <td>
                                    <select name="recept_categorie" id="recept_categorie" style="padding:4px 8px;">
                                        <option value="">-- Geen / later kiezen --</option>
                                        <?php foreach ( $categories as $cat ) : ?>
                                            <option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <th scope="row">
                                <strong>Zelf gemaakt</strong>
                            </th>
                            <td>
                                <label style="display:inline-flex;align-items:center;gap:8px;font-size:14px;">
                                    <input type="checkbox" name="is_zelf_gemaakt" value="1" />
                                    <span>Ik heb dit recept al eens zelf gemaakt</span>
                                </label>
                                <p class="description">Laat dit uitgevinkt staan als je het recept alleen bewaart en nog niet hebt getest.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:20px;padding-top:15px;border-top:1px solid #e5e7eb;">
                    <button type="submit" class="button button-primary button-hero" style="display:inline-flex;align-items:center;gap:8px;">
                        <span>📥 Haal Recept Op & Voeg Toe</span>
                    </button>
                </div>
            </form>
        </div>

        <div style="margin-top:24px;color:#6b7280;font-size:13px;line-height:1.6;">
            <strong>💡 Hoe werkt het?</strong>
            <ul style="list-style:disc;margin-left:20px;margin-top:6px;">
                <li>Gebruikt de officiële <strong>Schema.org Recipe structured data</strong> die 95%+ van foodblogs en kookwebsites aanbieden.</li>
                <li>Zet ingrediënten automatisch om naar het vereiste formaat: <code>Naam van ingrediënt | Hoeveelheid</code>.</li>
                <li>Draait 100% in PHP binnen WordPress en werkt direct op <strong>InfinityFree</strong> zonder externe software of Python.</li>
            </ul>
        </div>
    </div>
    <?php
}

// 4. De Scraper Engine
function rick_import_recipe_from_url( $url, $post_status = 'draft', $is_zelf_gemaakt = 0, $category_id = 0 ) {
    $response = wp_remote_get( $url, array(
        'timeout'     => 20,
        'redirection' => 5,
        'user-agent'  => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'headers'     => array(
            'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'nl-NL,nl;q=0.9,en-US;q=0.8,en;q=0.7',
        ),
        'sslverify'   => false,
    ) );

    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'http_failed', 'Kan de pagina niet bereiken: ' . $response->get_error_message() );
    }

    $response_code = wp_remote_retrieve_response_code( $response );
    if ( $response_code !== 200 ) {
        return new WP_Error( 'http_error', 'De website reageerde met statuscode ' . $response_code );
    }

    $html = wp_remote_retrieve_body( $response );
    if ( empty( $html ) ) {
        return new WP_Error( 'empty_body', 'De webpagina gaf geen inhoud terug.' );
    }

    // Bepaal bronnaam op basis van het domein
    $host = parse_url( $url, PHP_URL_HOST );
    $host = preg_replace( '/^www\./', '', strtolower( $host ) );
    $bron_naam = rick_format_source_name( $host );

    // Zoek JSON-LD Schema.org Recipe data
    $recipe_data = rick_extract_recipe_schema( $html );

    $title = '';
    $intro_tekst = '';
    $image_url = '';
    $bereidingstijd = '';
    $ingredienten_formatted = '';
    $bereidingswijze_formatted = '';
    $meta_info = 'Recept van ' . $bron_naam;
    $kcal = '';
    $eiwitten = '';
    $koolhydraten = '';
    $vetten = '';

    if ( ! empty( $recipe_data ) ) {
        // 1. Titel
        if ( ! empty( $recipe_data['name'] ) ) {
            $title = wp_strip_all_tags( html_entity_decode( $recipe_data['name'], ENT_QUOTES, 'UTF-8' ) );
        }

        // 2. Introductie
        if ( ! empty( $recipe_data['description'] ) ) {
            $intro_tekst = trim( html_entity_decode( $recipe_data['description'], ENT_QUOTES, 'UTF-8' ) );
        }

        // 3. Afbeelding
        if ( ! empty( $recipe_data['image'] ) ) {
            $image_url = rick_extract_image_url( $recipe_data['image'] );
        }

        // 4. Bereidingstijd
        $time_iso = ! empty( $recipe_data['totalTime'] ) ? $recipe_data['totalTime'] : ( ! empty( $recipe_data['cookTime'] ) ? $recipe_data['cookTime'] : ( ! empty( $recipe_data['prepTime'] ) ? $recipe_data['prepTime'] : '' ) );
        if ( ! empty( $time_iso ) ) {
            $bereidingstijd = rick_format_iso_duration( $time_iso );
        }

        // 5. Porties toevoegen aan meta_info indien beschikbaar
        if ( ! empty( $recipe_data['recipeYield'] ) ) {
            $yield = is_array( $recipe_data['recipeYield'] ) ? reset( $recipe_data['recipeYield'] ) : $recipe_data['recipeYield'];
            $yield = trim( wp_strip_all_tags( (string) $yield ) );
            if ( $yield ) {
                $meta_info .= ' • ' . ( is_numeric( $yield ) ? $yield . ' personen' : $yield );
            }
        }

        // 6. Ingrediënten omzetten naar GEMINI.md richtlijn: "Naam | Hoeveelheid"
        if ( ! empty( $recipe_data['recipeIngredient'] ) && is_array( $recipe_data['recipeIngredient'] ) ) {
            $ing_lines = array();
            foreach ( $recipe_data['recipeIngredient'] as $raw_ing ) {
                $parsed = rick_parse_ingredient_line( $raw_ing );
                if ( ! empty( $parsed ) ) {
                    $ing_lines[] = $parsed;
                }
            }
            $ingredienten_formatted = implode( "\n", $ing_lines );
        }

        // 7. Bereidingswijze
        if ( ! empty( $recipe_data['recipeInstructions'] ) ) {
            $bereidingswijze_formatted = rick_format_recipe_instructions( $recipe_data['recipeInstructions'] );
        }

        // 8. Voedingswaarden (optioneel)
        if ( ! empty( $recipe_data['nutrition'] ) && is_array( $recipe_data['nutrition'] ) ) {
            $nutr = $recipe_data['nutrition'];
            if ( ! empty( $nutr['calories'] ) ) {
                $kcal = preg_replace( '/[^0-9]/', '', (string) $nutr['calories'] );
            }
            if ( ! empty( $nutr['proteinContent'] ) ) {
                $eiwitten = trim( preg_replace( '/[^0-9\.,]/', '', (string) $nutr['proteinContent'] ) );
            }
            if ( ! empty( $nutr['carbohydrateContent'] ) ) {
                $koolhydraten = trim( preg_replace( '/[^0-9\.,]/', '', (string) $nutr['carbohydrateContent'] ) );
            }
            if ( ! empty( $nutr['fatContent'] ) ) {
                $vetten = trim( preg_replace( '/[^0-9\.,]/', '', (string) $nutr['fatContent'] ) );
            }
        }
    } else {
        // Fallback als er geen Schema.org JSON-LD is (bv. via OpenGraph tags)
        $title = rick_extract_meta_tag( $html, 'og:title' );
        if ( empty( $title ) && preg_match( '/<title[^>]*>(.*?)<\/title>/is', $html, $m ) ) {
            $title = wp_strip_all_tags( $m[1] );
        }
        $intro_tekst = rick_extract_meta_tag( $html, 'og:description' );
        $image_url = rick_extract_meta_tag( $html, 'og:image' );
    }

    if ( empty( $title ) ) {
        return new WP_Error( 'no_title', 'Kon geen recepttitel vinden op deze pagina.' );
    }

    // Maak het recept post aan
    $post_id = wp_insert_post( array(
        'post_title'   => $title,
        'post_content' => '',
        'post_status'  => $post_status,
        'post_type'    => 'recept',
    ), true );

    if ( is_wp_error( $post_id ) ) {
        return $post_id;
    }

    // Koppel categorie indien geselecteerd
    if ( $category_id > 0 ) {
        wp_set_post_terms( $post_id, array( $category_id ), 'recept_categorie' );
    }

    // Velden opslaan (zowel via ACF als native post meta voor maximale betrouwbaarheid)
    $fields = array(
        'meta_info'           => $meta_info,
        'recept_afbeelding'   => $image_url,
        'bereidingstijd'      => $bereidingstijd,
        'intro_tekst'         => $intro_tekst,
        'ingredienten'        => $ingredienten_formatted,
        'bereidingswijze'     => $bereidingswijze_formatted,
        'is_zelf_gemaakt'     => $is_zelf_gemaakt ? '1' : '0',
        'is_geautomatiseerd'  => '1',
        'bron_naam'           => $bron_naam,
        'bron_url'            => $url,
        'kcal'                => $kcal,
        'eiwitten'            => $eiwitten,
        'koolhydraten'        => $koolhydraten,
        'vetten'              => $vetten,
    );

    foreach ( $fields as $key => $val ) {
        if ( function_exists( 'update_field' ) ) {
            // ACF boolean handling
            if ( in_array( $key, array( 'is_zelf_gemaakt', 'is_geautomatiseerd' ) ) ) {
                update_field( $key, ( $val === '1' ? true : false ), $post_id );
            } else {
                update_field( $key, $val, $post_id );
            }
        }
        update_post_meta( $post_id, $key, $val );
    }

    return $post_id;
}

// 5. Helper functies voor parsing

function rick_format_source_name( $host ) {
    $map = array(
        'ah.nl'               => 'Allerhande',
        'lekkerensimpel.com'  => 'Lekker en Simpel',
        '24kitchen.nl'        => '24Kitchen',
        'rutgerbakt.nl'       => 'Rutger Bakt',
        'laurasbakery.nl'     => "Laura's Bakery",
        'smulweb.nl'          => 'Smulweb',
        'leukerecepten.nl'    => 'Leuke Recepten',
        'chickslovefood.com'  => 'Chickslovefood',
        'miljuschka.nl'       => 'Miljuschka',
        'bbcgoodfood.com'     => 'BBC Good Food',
    );

    if ( isset( $map[ $host ] ) ) {
        return $map[ $host ];
    }

    $parts = explode( '.', $host );
    return ucfirst( $parts[0] );
}

function rick_extract_recipe_schema( $html ) {
    if ( ! preg_match_all( '/<script\b[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches ) ) {
        return null;
    }

    foreach ( $matches[1] as $raw_json ) {
        $data = json_decode( trim( $raw_json ), true );
        if ( empty( $data ) || ! is_array( $data ) ) {
            continue;
        }

        $found = rick_find_recipe_in_json( $data );
        if ( ! empty( $found ) ) {
            return $found;
        }
    }

    return null;
}

function rick_find_recipe_in_json( $data ) {
    if ( ! is_array( $data ) ) {
        return null;
    }

    // Direct object check
    if ( isset( $data['@type'] ) ) {
        $type = is_array( $data['@type'] ) ? $data['@type'] : array( $data['@type'] );
        if ( in_array( 'Recipe', $type, true ) || in_array( 'recipe', array_map( 'strtolower', $type ), true ) ) {
            return $data;
        }
    }

    // @graph array
    if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
        foreach ( $data['@graph'] as $item ) {
            $res = rick_find_recipe_in_json( $item );
            if ( $res ) {
                return $res;
            }
        }
    }

    // List of items
    if ( isset( $data[0] ) ) {
        foreach ( $data as $item ) {
            $res = rick_find_recipe_in_json( $item );
            if ( $res ) {
                return $res;
            }
        }
    }

    return null;
}

function rick_extract_image_url( $image_data ) {
    if ( is_string( $image_data ) ) {
        return esc_url_raw( $image_data );
    }

    if ( is_array( $image_data ) ) {
        if ( isset( $image_data['url'] ) && is_string( $image_data['url'] ) ) {
            return esc_url_raw( $image_data['url'] );
        }
        if ( isset( $image_data[0] ) ) {
            return rick_extract_image_url( $image_data[0] );
        }
    }

    return '';
}

function rick_format_iso_duration( $iso ) {
    if ( preg_match( '/P(?:([0-9]+)D)?T?(?:([0-9]+)H)?(?:([0-9]+)M)?/i', $iso, $m ) ) {
        $days = ! empty( $m[1] ) ? (int) $m[1] : 0;
        $hours = ! empty( $m[2] ) ? (int) $m[2] : 0;
        $minutes = ! empty( $m[3] ) ? (int) $m[3] : 0;

        $total_hours = ( $days * 24 ) + $hours;

        if ( $total_hours > 0 && $minutes > 0 ) {
            return sprintf( '%d uur %d min', $total_hours, $minutes );
        } elseif ( $total_hours > 0 ) {
            return sprintf( '%d uur', $total_hours );
        } elseif ( $minutes > 0 ) {
            return sprintf( '%d minuten', $minutes );
        }
    }

    // Als het al een gewone tekst is
    return sanitize_text_field( $iso );
}

/**
 * Converteert een ingrediënt-regel naar "Naam | Hoeveelheid"
 * volgens de vereisten in GEMINI.md
 */
function rick_parse_ingredient_line( $line ) {
    $line = wp_strip_all_tags( html_entity_decode( $line, ENT_QUOTES, 'UTF-8' ) );
    $line = trim( preg_replace( '/^[•\*\-\s]+/u', '', $line ) );

    if ( empty( $line ) ) {
        return '';
    }

    // Breuktekens omzetten
    $fractions = array(
        '½' => '1/2',
        '¼' => '1/4',
        '¾' => '3/4',
        '⅓' => '1/3',
        '⅔' => '2/3',
        '⅛' => '1/8',
    );
    $line = strtr( $line, $fractions );

    // Eenheden patroon (Nederlands en Engels)
    $units = 'kg|kilo|gram|gr|g|ml|cl|dl|liter|l|el|tl|eetlepel|eetlepels|theelepel|theelepels|stuks|stuk|pak|pakje|pakjes|zak|zakje|zakjes|blik|blikje|blikjes|fles|flesje|pot|potje|mespunt|mespuntje|snuf|snufje|tenen|teen|teentje|teentjes|stengels|stengel|bekertje|bakje|bakjes|bosje|bosjes|plakjes|plakje|plak|plakken|handjes|handje|hand|kopjes|kopje|kop|scheut|scheutje|blaadjes|blaadje|takjes|takje|drops|drop|tbsp|tsp|cups|cup|oz|lbs|lb';

    // Patroon 1: Getal + optionele eenheid vooraan (bijv. "500 g bloem" of "2 el olijfolie" of "1 ui")
    if ( preg_match( '/^([\d\s\.,\/\-]+(?:' . $units . ')?)\s+(.*)$/iu', $line, $matches ) ) {
        $amount = trim( $matches[1] );
        $name = trim( $matches[2] );

        // Eerste letter van de naam als hoofdletter
        $name = ucfirst( $name );
        return "{$name} | {$amount}";
    }

    // Patroon 2: Alleen eenheid/beschrijving zonder duidelijk beginbedrag (bijv. "zout naar smaak")
    $name = ucfirst( $line );
    return "{$name} | ";
}

function rick_format_recipe_instructions( $instructions ) {
    $output = array();

    // Enkele string met HTML of regeleinden
    if ( is_string( $instructions ) ) {
        $cleaned = wp_strip_all_tags( html_entity_decode( $instructions, ENT_QUOTES, 'UTF-8' ) );
        $lines = preg_split( '/\r\n|\r|\n/', $cleaned );
        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( ! empty( $line ) ) {
                $output[] = '<p>' . esc_html( $line ) . '</p>';
            }
        }
        return implode( "\n", $output );
    }

    if ( is_array( $instructions ) ) {
        foreach ( $instructions as $step ) {
            // Als het een HowToSection is met stappen
            if ( isset( $step['@type'] ) && $step['@type'] === 'HowToSection' ) {
                if ( ! empty( $step['name'] ) ) {
                    $output[] = '<h2>' . esc_html( $step['name'] ) . '</h2>';
                }
                if ( ! empty( $step['itemListElement'] ) && is_array( $step['itemListElement'] ) ) {
                    foreach ( $step['itemListElement'] as $sub_step ) {
                        $text = rick_get_step_text( $sub_step );
                        if ( $text ) {
                            $output[] = '<p>' . esc_html( $text ) . '</p>';
                        }
                    }
                }
                continue;
            }

            // Normale HowToStep of tekstregel
            $text = rick_get_step_text( $step );
            if ( ! empty( $text ) ) {
                $output[] = '<p>' . esc_html( $text ) . '</p>';
            }
        }
    }

    return implode( "\n", $output );
}

function rick_get_step_text( $step ) {
    if ( is_string( $step ) ) {
        return trim( wp_strip_all_tags( html_entity_decode( $step, ENT_QUOTES, 'UTF-8' ) ) );
    }
    if ( is_array( $step ) ) {
        if ( ! empty( $step['text'] ) ) {
            return trim( wp_strip_all_tags( html_entity_decode( $step['text'], ENT_QUOTES, 'UTF-8' ) ) );
        }
        if ( ! empty( $step['name'] ) ) {
            return trim( wp_strip_all_tags( html_entity_decode( $step['name'], ENT_QUOTES, 'UTF-8' ) ) );
        }
    }
    return '';
}

function rick_extract_meta_tag( $html, $property ) {
    if ( preg_match( '/<meta\b[^>]*property=["\']' . preg_quote( $property, '/' ) . '["\'][^>]*content=["\'](.*?)["\']/i', $html, $m ) ) {
        return html_entity_decode( trim( $m[1] ), ENT_QUOTES, 'UTF-8' );
    }
    if ( preg_match( '/<meta\b[^>]*name=["\']' . preg_quote( $property, '/' ) . '["\'][^>]*content=["\'](.*?)["\']/i', $html, $m ) ) {
        return html_entity_decode( trim( $m[1] ), ENT_QUOTES, 'UTF-8' );
    }
    return '';
}

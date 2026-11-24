<?php

function rick_get_recept_field( $field_name, $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();

    if ( function_exists('get_field') ) {
        $value = get_field( $field_name, $post_id );

        if ( $value !== null && $value !== '' ) {
            return $value;
        }
    }

    return get_post_meta( $post_id, $field_name, true );
}

function rick_get_recept_primary_category( $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $terms = get_the_terms( $post_id, 'recept_categorie' );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return null;
    }

    return array_shift( $terms );
}

/**
 * Voorgedefinieerde kleurentabel voor alle receptcategorieën.
 */
function rick_get_default_category_colors() {
    return array(
        'avondeten'        => '#e11d48', // Warm Koraalrood / Avondeten
        'beslagrecepten'   => '#d97706', // Warm Goudgeel / Beslag
        'brood'            => '#92400e', // Authentiek Broodbruin
        'drinken'          => '#0284c7', // Oceaanblauw / Verfrissend
        'gebak'            => '#db2777', // Frambozenroze / Zoet gebak
        'hartig'           => '#15803d', // Saliegroen / Verse kruiden
        'koek'             => '#c2410c', // Kaneeloranje / Koekjes
        'ninja-creami'     => '#0891b2', // IJzig Cyaan / IJs & Creami
        'saus'             => '#b91c1c', // Diep Robijnrood / Sauzen
        'sinterklaas'      => '#7c3aed', // Feestelijk Paars / Sinterklaas
        'smoothie'         => '#65a30d', // Tropisch Limoengroen / Smoothies
        'vulling-topping'  => '#8b5cf6', // Romig Lavendel / Toppings
    );
}

/**
 * Haal de kleur op van een specifieke term of term slug.
 */
function rick_get_term_color( $term_or_slug ) {
    $term = null;

    if ( is_object( $term_or_slug ) ) {
        $term = $term_or_slug;
    } elseif ( is_numeric( $term_or_slug ) ) {
        $term = get_term( (int) $term_or_slug, 'recept_categorie' );
    } elseif ( is_string( $term_or_slug ) ) {
        $term = get_term_by( 'slug', $term_or_slug, 'recept_categorie' );
    }

    if ( $term && ! is_wp_error( $term ) ) {
        $color = get_term_meta( $term->term_id, 'rick_category_color', true );
        if ( ! empty( $color ) ) {
            return sanitize_hex_color( $color );
        }

        $defaults = rick_get_default_category_colors();
        if ( isset( $defaults[ $term->slug ] ) ) {
            return $defaults[ $term->slug ];
        }
    }

    return '#d97706';
}

function rick_get_recept_primary_category_color( $post_id = null ) {
    $term = rick_get_recept_primary_category( $post_id );

    if ( ! $term ) {
        return '#d97706';
    }

    return rick_get_term_color( $term );
}

/**
 * Zoek recept IDs op basis van titel, ingrediënten, intro, meta_info en bereidingswijze.
 */
function rick_search_recept_ids( $search_term ) {
    $search_term = trim( $search_term );
    if ( empty( $search_term ) ) {
        return array();
    }

    $title_ids = get_posts( array(
        'post_type'      => 'recept',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
        's'              => $search_term,
    ) );

    $meta_ids = get_posts( array(
        'post_type'      => 'recept',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'ingredienten',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'intro_tekst',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'meta_info',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'bereidingswijze',
                'value'   => $search_term,
                'compare' => 'LIKE',
            ),
        ),
    ) );

    return array_unique( array_merge( (array) $title_ids, (array) $meta_ids ) );
}

/**
 * Controleert of een DOM-node leeg is (alleen witruimte, NBSP of lege <br>).
 */
function rick_is_empty_dom_node( $node ) {
    if ( $node instanceof DOMText ) {
        $text = str_replace( "\xC2\xA0", ' ', $node->nodeValue );
        return trim( $text ) === '';
    }

    if ( ! ( $node instanceof DOMElement ) ) {
        return true;
    }

    $tag = strtolower( $node->tagName );
    if ( $tag === 'br' ) {
        return true;
    }

    if ( in_array( $tag, array( 'p', 'div', 'span' ), true ) ) {
        $text = str_replace( "\xC2\xA0", ' ', $node->textContent );
        if ( trim( $text ) !== '' ) {
            return false;
        }

        foreach ( $node->childNodes as $child ) {
            if ( $child instanceof DOMElement && strtolower( $child->tagName ) !== 'br' ) {
                return false;
            }
        }

        return true;
    }

    return false;
}

/**
 * Veilige string-lengte helper (werkt ook als mbstring niet actief is).
 */
function rick_safe_strlen( $text ) {
    return function_exists( 'mb_strlen' ) ? mb_strlen( $text, 'UTF-8' ) : strlen( $text );
}

/**
 * Controleert of een DOM-node een tussenkop is (h1-h6 of een losse vetgedrukte kop-paragraaf).
 */
function rick_is_step_heading_node( $node ) {
    if ( ! ( $node instanceof DOMElement ) ) {
        return false;
    }

    $tag = strtolower( $node->tagName );
    if ( in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ) {
        return true;
    }

    if ( $tag === 'p' ) {
        $text = trim( str_replace( "\xC2\xA0", ' ', $node->textContent ) );
        if ( $text === '' || rick_safe_strlen( $text ) > 80 ) {
            return false;
        }

        $meaningful_children = array();
        foreach ( $node->childNodes as $child ) {
            if ( rick_is_empty_dom_node( $child ) ) {
                continue;
            }
            $meaningful_children[] = $child;
        }

        if ( count( $meaningful_children ) === 1 && $meaningful_children[0] instanceof DOMElement ) {
            $child_tag = strtolower( $meaningful_children[0]->tagName );
            if ( in_array( $child_tag, array( 'strong', 'b' ), true ) ) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Bepaalt het maximale aantal list-items binnen een lijstnode of zijn kinderen.
 */
function rick_count_max_list_items( $nodes ) {
    $max = 0;

    foreach ( $nodes as $node ) {
        if ( ! ( $node instanceof DOMElement ) ) {
            continue;
        }

        $tag = strtolower( $node->tagName );
        if ( $tag === 'ul' || $tag === 'ol' ) {
            $count = 0;
            foreach ( $node->childNodes as $child ) {
                if ( $child instanceof DOMElement && strtolower( $child->tagName ) === 'li' ) {
                    $count++;
                }
            }
            if ( $count > $max ) {
                $max = $count;
            }
        }
    }

    return $max;
}

/**
 * Groepeert stappen in de bereidingswijze zodat koppen ("Bereidingswijze", "Stap 1", enz.)
 * bij het printen nooit los onderaan een pagina blijven staan zonder bijbehorende tekst.
 *
 * @param string $html HTML uit het bereidingswijze-veld.
 * @return array{lead: string, rest: string}
 */
function rick_prepare_bereidingswijze_print_parts( $html ) {
    $html = (string) $html;
    if ( trim( $html ) === '' ) {
        return array( 'lead' => '', 'rest' => '' );
    }

    if ( ! class_exists( 'DOMDocument' ) || strpos( $html, '<' ) === false ) {
        return array( 'lead' => '', 'rest' => $html );
    }

    $dom = new DOMDocument( '1.0', 'UTF-8' );
    $previous_errors = libxml_use_internal_errors( true );
    $wrapped = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><div id="rick-steps-root">' . $html . '</div></body></html>';
    $loaded = $dom->loadHTML( $wrapped );
    libxml_clear_errors();
    libxml_use_internal_errors( $previous_errors );

    if ( ! $loaded ) {
        return array( 'lead' => '', 'rest' => $html );
    }

    $root = $dom->getElementById( 'rick-steps-root' );
    if ( ! $root ) {
        return array( 'lead' => '', 'rest' => $html );
    }

    $groups = array();
    $current_group = null;

    foreach ( $root->childNodes as $node ) {
        $is_empty = rick_is_empty_dom_node( $node );
        $is_heading = ! $is_empty && rick_is_step_heading_node( $node );

        if ( $is_heading ) {
            if ( $current_group !== null && $current_group['has_heading'] && ! $current_group['has_content'] ) {
                // Opeenvolgende koppen zonder tussenliggende tekst bij elkaar houden.
                $current_group['nodes'][] = $node;
            } else {
                if ( $current_group !== null ) {
                    $groups[] = $current_group;
                }
                $current_group = array(
                    'has_heading' => true,
                    'has_content' => false,
                    'nodes'       => array( $node ),
                );
            }
            continue;
        }

        if ( $is_empty ) {
            if ( $current_group !== null && $current_group['has_heading'] ) {
                $current_group['nodes'][] = $node;
            }
            continue;
        }

        if ( $current_group !== null && $current_group['has_heading'] ) {
            $current_group['has_content'] = true;
            $current_group['nodes'][] = $node;
        } else {
            if ( $current_group !== null ) {
                $groups[] = $current_group;
                $current_group = null;
            }
            $groups[] = array(
                'has_heading' => false,
                'has_content' => true,
                'nodes'       => array( $node ),
            );
        }
    }

    if ( $current_group !== null ) {
        $groups[] = $current_group;
    }

    if ( empty( $groups ) ) {
        return array( 'lead' => '', 'rest' => $html );
    }

    $serialize_nodes = function ( $nodes ) use ( $dom ) {
        $out = '';
        foreach ( $nodes as $n ) {
            $out .= $dom->saveHTML( $n );
        }
        return $out;
    };

    $render_group = function ( $group ) use ( $serialize_nodes ) {
        if ( ! $group['has_heading'] ) {
            return array(
                'full'       => $serialize_nodes( $group['nodes'] ),
                'lead_only'  => null,
                'tail_only'  => null,
                'is_compact' => ( rick_safe_strlen( trim( implode( ' ', array_map( function ( $n ) {
                    return $n->textContent;
                }, $group['nodes'] ) ) ) ) <= 500 && rick_count_max_list_items( $group['nodes'] ) <= 4 ),
            );
        }

        $content_nodes = array();
        $total_text_len = 0;

        foreach ( $group['nodes'] as $n ) {
            $total_text_len += rick_safe_strlen( trim( str_replace( "\xC2\xA0", ' ', $n->textContent ) ) );
            if ( ! rick_is_empty_dom_node( $n ) && ! rick_is_step_heading_node( $n ) ) {
                $content_nodes[] = $n;
            }
        }

        $max_list_items = rick_count_max_list_items( $content_nodes );
        $is_compact = ( $total_text_len <= 900 && count( $content_nodes ) <= 4 && $max_list_items <= 6 );

        if ( $is_compact ) {
            $wrapped_html = '<div class="recipe-step-group">' . $serialize_nodes( $group['nodes'] ) . '</div>';
            return array(
                'full'       => $wrapped_html,
                'lead_only'  => $wrapped_html,
                'tail_only'  => '',
                'is_compact' => true,
            );
        }

        // Lange stap: houd de kop vast aan het eerste tekstblok, laat de rest vrij doorlopen.
        $lead_nodes = array();
        $tail_nodes = array();
        $found_first_content = false;

        foreach ( $group['nodes'] as $n ) {
            if ( ! $found_first_content ) {
                $is_content = ! rick_is_empty_dom_node( $n ) && ! rick_is_step_heading_node( $n );
                if ( $is_content ) {
                    $found_first_content = true;
                    if ( rick_count_max_list_items( array( $n ) ) <= 4 ) {
                        $lead_nodes[] = $n;
                    } else {
                        $tail_nodes[] = $n;
                    }
                } else {
                    $lead_nodes[] = $n;
                }
            } else {
                $tail_nodes[] = $n;
            }
        }

        $lead_html = '<div class="recipe-step-lead">' . $serialize_nodes( $lead_nodes ) . '</div>';
        $tail_html = $serialize_nodes( $tail_nodes );

        return array(
            'full'       => '<div class="recipe-step-group recipe-step-group--long">' . $lead_html . $tail_html . '</div>',
            'lead_only'  => $lead_html,
            'tail_only'  => $tail_html,
            'is_compact' => false,
        );
    };

    $first = $render_group( $groups[0] );
    $lead_html = '';
    $rest_parts = array();

    if ( $groups[0]['has_heading'] ) {
        $lead_html = $first['lead_only'];
        if ( ! empty( $first['tail_only'] ) ) {
            $rest_parts[] = $first['tail_only'];
        }
    } elseif ( $first['is_compact'] ) {
        $lead_html = $first['full'];
    } else {
        $rest_parts[] = $first['full'];
    }

    for ( $i = 1; $i < count( $groups ); $i++ ) {
        $rendered = $render_group( $groups[ $i ] );
        $rest_parts[] = $rendered['full'];
    }

    return array(
        'lead' => $lead_html,
        'rest' => implode( "\n", $rest_parts ),
    );
}
<?php
/**
 * Plugin Name: Rick Recepten PPT Export
 * Description: Exporteert alle Recepten CPT naar een PowerPoint (216×303 mm, één slide per recept).
 * Version: 2.0
 * Author: Rick
 */

if (!defined('ABSPATH')) {
    exit;
}

// Flexibele autoloader check voor jouw specifieke Docker structuur
if (file_exists('/var/www/html/vendor/autoload.php')) {
    require_once '/var/www/html/vendor/autoload.php';
} else {
    require_once dirname(__FILE__, 4) . '/vendor/autoload.php';
}

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Slide\Background\Color as BgColor;

// ─────────────────────────────────────────────
// 1. Admin-menu pagina
// ─────────────────────────────────────────────
add_action('admin_menu', 'rick_ppt_admin_menu');
function rick_ppt_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=recept',
        'Exporteer naar PowerPoint',
        '📄 Exporteer PPTX',
        'manage_options',
        'rick-ppt-export',
        'rick_ppt_export_page'
    );
}

function rick_ppt_export_page() {
    $recepten = get_posts([
        'post_type'      => 'recept',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $export_url = wp_nonce_url(
        admin_url('admin-post.php?action=rick_export_alle_recepten'),
        'rick_ppt_export_nonce'
    );
    ?>
    <div class="wrap">
        <h1>📄 Recepten exporteren naar PowerPoint</h1>
        <p>Exporteert alle gepubliceerde recepten naar één PowerPoint-bestand (216 × 303 mm, één slide per recept).</p>

        <table class="wp-list-table widefat fixed striped" style="max-width:600px; margin-bottom:20px;">
            <thead>
                <tr><th>Recept</th><th>Bereidingstijd</th></tr>
            </thead>
            <tbody>
                <?php if (empty($recepten)) : ?>
                    <tr><td colspan="2"><em>Geen gepubliceerde recepten gevonden.</em></td></tr>
                <?php else : ?>
                    <?php foreach ($recepten as $recept) : ?>
                        <tr>
                            <td><?php echo esc_html($recept->post_title); ?></td>
                            <td><?php echo esc_html(get_field('bereidingstijd', $recept->ID) ?: '–'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($recepten)) : ?>
            <a href="<?php echo esc_url($export_url); ?>" class="button button-primary button-hero">
                ⬇️ Download PowerPoint (<?php echo count($recepten); ?> recepten)
            </a>
        <?php endif; ?>
    </div>
    <?php
}

// ─────────────────────────────────────────────
// 2. Export handler
// ─────────────────────────────────────────────
add_action('admin_post_rick_export_alle_recepten', 'rick_handle_bulk_ppt_export');
function rick_handle_bulk_ppt_export() {
    if (!current_user_can('manage_options') ||
        !isset($_GET['_wpnonce']) ||
        !wp_verify_nonce($_GET['_wpnonce'], 'rick_ppt_export_nonce')) {
        wp_die('Beveiligingscontrole mislukt.');
    }

    $recepten = get_posts([
        'post_type'      => 'recept',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    if (empty($recepten)) {
        wp_die('Geen gepubliceerde recepten gevonden.');
    }

    // ── Presentatie aanmaken ──
    $prs = new PhpPresentation();

    // 216 × 303 mm in EMU (1 mm = 36000 EMU)
    $breedte_emu = 216 * 36000; // 7.776.000
    $hoogte_emu  = 303 * 36000; // 10.908.000

    $layout = $prs->getLayout();
    $layout->setDocumentLayout(DocumentLayout::LAYOUT_CUSTOM);
    $layout->setCX($breedte_emu, DocumentLayout::UNIT_EMU);
    $layout->setCY($hoogte_emu, DocumentLayout::UNIT_EMU);

    // Verwijder de standaard lege slide
    $prs->removeSlideByIndex(0);

    // ── Kleuren & stijl ──
    // Warm, bakkerij-geïnspireerd palet
    $kleur_donker    = 'FF2C2C2C'; // bijna zwart – titels
    $kleur_accent    = 'FFB85042'; // terracotta – koppen
    $kleur_licht     = 'FFF5F0EB'; // warm gebroken wit – achtergrond
    $kleur_subtitel  = 'FF7A6A60'; // warm grijs – meta info
    $kleur_wit       = 'FFFFFFFF';
    $kleur_tip_bg    = 'FFFDF3EE'; // licht perzik – tip blok

    foreach ($recepten as $recept) {
        $post_id = $recept->ID;

        // ACF velden
        $titel          = get_the_title($post_id);
        $ondertitel     = get_field('meta_info', $post_id) ?: '';
        $afbeelding_url = get_field('recept_afbeelding', $post_id) ?: '';
        $bereidingstijd = get_field('bereidingstijd', $post_id) ?: '';
        $intro          = wp_strip_all_tags(get_field('intro_tekst', $post_id) ?: '');
        $ingredienten   = get_field('ingredienten', $post_id) ?: '';
        $bereidingswijze = wp_strip_all_tags(get_field('bereidingswijze', $post_id) ?: '');
        $bakker_tip     = get_field('bakker_tip', $post_id) ?: '';

        // Primary color (uit categorie)
        $primary_hex = 'FFD97706'; // Default fallback
        $terms = get_the_terms($post_id, 'recept_categorie');
        if (!empty($terms) && !is_wp_error($terms)) {
            $term = array_shift($terms);
            $color = get_term_meta($term->term_id, 'rick_category_color', true);
            if ($color) {
                $primary_hex = rick_parse_color($color);
            }
        }

        // Ingrediënten parsen
        $ingredienten_regels = [];
        if ($ingredienten) {
            $raw_regels = preg_split('/\r\n|\r|\n|<br\s*\/?>/', $ingredienten);
            foreach ($raw_regels as $regel) {
                $regel = wp_strip_all_tags(trim($regel));
                if ($regel === '') continue;
                $parts = explode('|', $regel, 2);
                $ingredienten_regels[] = [
                    'naam'      => trim($parts[0] ?? ''),
                    'hoeveelheid' => trim($parts[1] ?? ''),
                ];
            }
        }

        // ── Layout variabelen ──
        $marge       = 10 * 36000;   // 10 mm marge rondom container
        $container_w = $breedte_emu - (2 * $marge);
        $container_h = $hoogte_emu - (2 * $marge);
        $pad         = 10 * 36000; // padding binnen de container
        $content_x   = $marge + $pad;
        $content_w   = $container_w - (2 * $pad);
        $y           = $marge + $pad;

        // ── Functie voor Nieuwe Slide ──
        $nieuwe_slide = function() use (&$prs, &$slide, &$y, $marge, $pad, $container_w, $container_h) {
            $slide = $prs->createSlide();
            $bg = new BgColor();
            $bg->setColor(new Color('FFF9FAFB'));
            $slide->setBackground($bg);
            rick_rect($slide, $marge, $marge, $container_w, $container_h, 'FFFFFFFF');
            $y = $marge + $pad;
        };

        // ── Paginering Helper ──
        $check_page_break = function($required_height) use (&$y, $marge, $container_h, $pad, $nieuwe_slide) {
            if ($y + $required_height > $marge + $container_h - $pad) {
                $nieuwe_slide();
            }
        };

        // Start eerste slide van dit recept
        $nieuwe_slide();

        // ── HEADER ──
        $heeft_afbeelding = !empty($afbeelding_url);
        $img_pad = $heeft_afbeelding ? rick_url_to_path($afbeelding_url) : false;
        $heeft_afbeelding = $img_pad && file_exists($img_pad);

        $col_text_w = $heeft_afbeelding ? ($content_w * 0.55) : $content_w;
        $header_start_y = $y;

        // Meta info
        if ($ondertitel) {
            $req_h = 7 * 36000;
            $check_page_break($req_h);
            $shape_meta = $slide->createRichTextShape();
            $shape_meta->setWidth(rick_px($col_text_w))->setHeight(rick_px(6 * 36000));
            $shape_meta->setOffsetX(rick_px($content_x))->setOffsetY(rick_px($y));
            $shape_meta->getActiveParagraph()->getAlignment()->setHorizontal($heeft_afbeelding ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER);
            $run = $shape_meta->createTextRun($ondertitel);
            $run->getFont()->setName('Segoe UI')->setItalic(true)->setSize(11)->setColor(new Color('FF6B7280'));
            $y += $req_h;
        }

        // Title
        $req_h = 18 * 36000;
        $check_page_break($req_h);
        $shape_titel = $slide->createRichTextShape();
        $shape_titel->setWidth(rick_px($col_text_w))->setHeight(rick_px(16 * 36000));
        $shape_titel->setOffsetX(rick_px($content_x))->setOffsetY(rick_px($y));
        $shape_titel->getActiveParagraph()->getAlignment()->setHorizontal($heeft_afbeelding ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER);
        $run = $shape_titel->createTextRun($titel);
        $run->getFont()->setName('Segoe UI')->setBold(true)->setSize(22)->setColor(new Color($primary_hex));
        $y += $req_h;

        // Badge (Bereidingstijd)
        if ($bereidingstijd) {
            $req_h = 10 * 36000;
            $check_page_break($req_h);
            $badge_w = 60 * 36000;
            $badge_x = $heeft_afbeelding ? $content_x : $content_x + ($content_w - $badge_w) / 2;
            
            rick_rect($slide, $badge_x, $y, $badge_w, 7 * 36000, 'FFFEF3C7');
            $shape_badge = $slide->createRichTextShape();
            $shape_badge->setWidth(rick_px($badge_w))->setHeight(rick_px(7 * 36000));
            $shape_badge->setOffsetX(rick_px($badge_x))->setOffsetY(rick_px($y + 1*36000));
            $shape_badge->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $run = $shape_badge->createTextRun('⏱ ' . $bereidingstijd);
            $run->getFont()->setName('Segoe UI')->setBold(true)->setSize(9)->setColor(new Color('FF92400E'));
            $y += $req_h;
        }

        $header_h = $y - $header_start_y;

        // Afbeelding
        if ($heeft_afbeelding) {
            $img_w = $content_w * 0.40;
            $img_h = 45 * 36000;
            $img_x = $content_x + $content_w - $img_w;
            
            $shape_img = $slide->createDrawingShape();
            $shape_img->setPath($img_pad);
            rick_set_emu($shape_img, $img_w, $img_h, $img_x, $header_start_y);
            
            if ($img_h > $header_h) {
                $y = $header_start_y + $img_h + (5 * 36000);
            }
        }

        // Border bottom header
        $check_page_break(6 * 36000);
        rick_lijn($slide, $content_x, $y, $content_w, 'FFF3F4F6');
        $y += 6 * 36000;

        // ── INTRO ──
        if ($intro) {
            $intro_h = 22 * 36000;
            $check_page_break($intro_h + 6 * 36000);
            
            rick_rect($slide, $content_x, $y, 2 * 36000, $intro_h, $primary_hex);
            rick_rect($slide, $content_x + (2 * 36000), $y, $content_w - (2 * 36000), $intro_h, 'FFFFFBEB');
            
            $shape_intro = $slide->createRichTextShape();
            $shape_intro->setWidth(rick_px($content_w - 6 * 36000))->setHeight(rick_px($intro_h));
            $shape_intro->setOffsetX(rick_px($content_x + 4 * 36000))->setOffsetY(rick_px($y + 2*36000));
            $run = $shape_intro->createTextRun($intro);
            $run->getFont()->setName('Segoe UI')->setSize(10)->setColor(new Color('FF374151'));
            
            $y += $intro_h + (6 * 36000);
        }

        // ── INGREDIËNTEN ──
        if (!empty($ingredienten_regels)) {
            $check_page_break(20 * 36000);
            
            $h2 = $slide->createRichTextShape();
            $h2->setWidth(rick_px($content_w))->setHeight(rick_px(8 * 36000));
            $h2->setOffsetX(rick_px($content_x))->setOffsetY(rick_px($y));
            $run = $h2->createTextRun('Ingrediënten');
            $run->getFont()->setName('Segoe UI')->setBold(true)->setSize(14)->setColor(new Color('FF1F2937'));
            $y += 8 * 36000;
            
            rick_lijn($slide, $content_x, $y, $content_w, 'FFE5E7EB');
            $y += 4 * 36000;
            
            $row_h = 7 * 36000;
            
            // Header row
            rick_rect($slide, $content_x, $y, $content_w, $row_h, 'FFF1F5F9');
            $th1 = $slide->createRichTextShape();
            $th1->setWidth(rick_px($content_w/2))->setHeight(rick_px($row_h));
            $th1->setOffsetX(rick_px($content_x + 2*36000))->setOffsetY(rick_px($y + 1*36000));
            $th1->createTextRun('Ingrediënt')->getFont()->setName('Segoe UI')->setBold(true)->setSize(10)->setColor(new Color('FF475569'));
            
            $th2 = $slide->createRichTextShape();
            $th2->setWidth(rick_px($content_w/2))->setHeight(rick_px($row_h));
            $th2->setOffsetX(rick_px($content_x + $content_w/2))->setOffsetY(rick_px($y + 1*36000));
            $th2->createTextRun('Hoeveelheid')->getFont()->setName('Segoe UI')->setBold(true)->setSize(10)->setColor(new Color('FF475569'));
            
            $y += $row_h;
            
            $i = 0;
            foreach ($ingredienten_regels as $ing) {
                $check_page_break($row_h);
                
                $bg_color = ($i % 2 === 0) ? 'FFFFF7ED' : 'FFFFFDF8';
                rick_rect($slide, $content_x, $y, $content_w, $row_h, $bg_color);
                
                $td1 = $slide->createRichTextShape();
                $td1->setWidth(rick_px($content_w/2))->setHeight(rick_px($row_h));
                $td1->setOffsetX(rick_px($content_x + 2*36000))->setOffsetY(rick_px($y + 1*36000));
                $td1->createTextRun($ing['naam'])->getFont()->setName('Segoe UI')->setSize(10)->setColor(new Color('FF374151'));
                
                $td2 = $slide->createRichTextShape();
                $td2->setWidth(rick_px($content_w/2))->setHeight(rick_px($row_h));
                $td2->setOffsetX(rick_px($content_x + $content_w/2))->setOffsetY(rick_px($y + 1*36000));
                $td2->createTextRun($ing['hoeveelheid'])->getFont()->setName('Segoe UI')->setSize(10)->setColor(new Color('FF374151'));
                
                rick_lijn($slide, $content_x, $y + $row_h, $content_w, 'FFEDF2F7');
                $y += $row_h;
                $i++;
            }
            $y += 6 * 36000;
        }

        // ── BEREIDINGSWIJZE ──
        if ($bereidingswijze) {
            $check_page_break(16 * 36000);
            
            $h2 = $slide->createRichTextShape();
            $h2->setWidth(rick_px($content_w))->setHeight(rick_px(8 * 36000));
            $h2->setOffsetX(rick_px($content_x))->setOffsetY(rick_px($y));
            $run = $h2->createTextRun('Bereidingswijze');
            $run->getFont()->setName('Segoe UI')->setBold(true)->setSize(14)->setColor(new Color('FF1F2937'));
            $y += 8 * 36000;
            
            rick_lijn($slide, $content_x, $y, $content_w, 'FFE5E7EB');
            $y += 4 * 36000;
            
            $stappen = preg_split('/\r\n|\r|\n/', $bereidingswijze);
            foreach ($stappen as $stap) {
                $stap = trim($stap);
                if ($stap === '') continue;
                
                $check_page_break(10 * 36000); // approx height per stap
                
                $step_shape = $slide->createRichTextShape();
                $step_shape->setWidth(rick_px($content_w))->setHeight(rick_px(8 * 36000));
                $step_shape->setOffsetX(rick_px($content_x))->setOffsetY(rick_px($y));
                
                if (strpos($stap, '##') === 0) {
                    $stap_tekst = trim(ltrim($stap, '#'));
                    $run = $step_shape->createTextRun($stap_tekst);
                    $run->getFont()->setName('Segoe UI')->setBold(true)->setSize(11)->setColor(new Color($primary_hex));
                    $y += 8 * 36000;
                } else {
                    $run = $step_shape->createTextRun('• ' . $stap);
                    $run->getFont()->setName('Segoe UI')->setSize(10)->setColor(new Color('FF374151'));
                    $y += 8 * 36000;
                }
            }
            $y += 4 * 36000;
        }

        // ── BAKKER TIP ──
        if ($bakker_tip) {
            $tip_h = 24 * 36000;
            $check_page_break($tip_h + 4 * 36000);
            
            // Achtergrond tip blok
            rick_rect($slide, $content_x, $y, $content_w, $tip_h, 'FFF0FDF4');
            rick_lijn($slide, $content_x, $y, $content_w, 'FFBBF7D0');
            rick_lijn($slide, $content_x, $y + $tip_h, $content_w, 'FFBBF7D0');
            
            $shape_tip = $slide->createRichTextShape();
            $shape_tip->setWidth(rick_px($content_w - 4*36000))->setHeight(rick_px($tip_h));
            $shape_tip->setOffsetX(rick_px($content_x + 2*36000))->setOffsetY(rick_px($y + 2*36000));
            
            $para1 = $shape_tip->createParagraph();
            $run1 = $para1->createTextRun('💡 Tip van de bakker:');
            $run1->getFont()->setName('Segoe UI')->setBold(true)->setSize(10)->setColor(new Color('FF166534'));
            
            $para2 = $shape_tip->createParagraph();
            $run2 = $para2->createTextRun(wp_strip_all_tags($bakker_tip));
            $run2->getFont()->setName('Segoe UI')->setSize(10)->setColor(new Color('FF1E293B'));
            
            $y += $tip_h + (4 * 36000);
        }
    }

    // ── Download forceren ──
    $bestandsnaam = 'recepten-export-' . date('Y-m-d') . '.pptx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
    header('Content-Disposition: attachment; filename="' . $bestandsnaam . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $writer = IOFactory::createWriter($prs, 'PowerPoint2007');
    $writer->save('php://output');
    exit;
}

// ─────────────────────────────────────────────
// Helpers: Eenheden en vormen
// ─────────────────────────────────────────────

function rick_parse_color($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 6) {
        return 'FF' . $hex;
    }
    return $hex;
}

/**
 * EMU naar Pixels (9525 EMU = 1 Pixel bij 96 DPI)
 */
function rick_px($emu) {
    return (int) round($emu / 9525);
}

// ─────────────────────────────────────────────
// Helper: gevulde rechthoek tekenen
// ─────────────────────────────────────────────
function rick_rect($slide, $x, $y, $breedte, $hoogte, $hex_kleur) {
    $shape = $slide->createRichTextShape();
    $shape->setWidth(rick_px($breedte))->setHeight(rick_px($hoogte));
    $shape->setOffsetX(rick_px($x))->setOffsetY(rick_px($y));
    $shape->setInsetLeft(0)->setInsetRight(0)
          ->setInsetTop(0)->setInsetBottom(0);

    $fill = $shape->getFill();
    $fill->setFillType(Fill::FILL_SOLID);
    $fill->setStartColor(new Color(rick_parse_color($hex_kleur)));
    // Zorg dat de border onzichtbaar is
    $shape->getBorder()->setLineStyle(Border::LINE_NONE);

    return $shape;
}

// ─────────────────────────────────────────────
// Helper: horizontale lijn tekenen
// ─────────────────────────────────────────────
function rick_lijn($slide, $x, $y, $breedte, $hex_kleur) {
    $shape = $slide->createLineShape(rick_px($x), rick_px($y), rick_px($x + $breedte), rick_px($y));
    $shape->getBorder()
        ->setLineStyle(Border::LINE_SINGLE)
        ->setLineWidth(1)
        ->setColor(new Color(rick_parse_color($hex_kleur)));
    return $shape;
}

// ─────────────────────────────────────────────
// Helper: DrawingShape afmetingen in EMU zetten
// PHPPresentation DrawingShape gebruikt interne pixels
// ─────────────────────────────────────────────
function rick_set_emu($shape, $breedte_emu, $hoogte_emu, $offset_x_emu, $offset_y_emu) {
    $shape->setWidth(rick_px($breedte_emu));
    $shape->setHeight(rick_px($hoogte_emu));
    $shape->setOffsetX(rick_px($offset_x_emu));
    $shape->setOffsetY(rick_px($offset_y_emu));
}

// ─────────────────────────────────────────────
// Helper: WordPress upload-URL naar lokaal pad
// ─────────────────────────────────────────────
function rick_url_to_path($url) {
    if (empty($url)) return false;
    $upload_dir = wp_upload_dir();
    $base_url   = $upload_dir['baseurl'];
    $base_dir   = $upload_dir['basedir'];
    if (strpos($url, $base_url) === 0) {
        return str_replace($base_url, $base_dir, $url);
    }
    return false;
}
<?php
/**
 * Plugin Name: Rick Recepten PPT Export
 * Description: Exporteert alle Recepten CPT naar een PowerPoint via PptxGenJS.
 * Version: 3.0
 * Author: Rick
 */

if (!defined('ABSPATH')) {
    exit;
}

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

// ─────────────────────────────────────────────
// 2. UI & Data van de Admin Pagina
// ─────────────────────────────────────────────
function rick_ppt_export_page() {
    // Haal alle recepten op voor JavaScript
    $recepten = get_posts([
        'post_type'      => 'recept',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $recepten_data = [];

    foreach ($recepten as $recept) {
        $post_id = $recept->ID;
        
        $afbeelding_url = get_field('recept_afbeelding', $post_id) ?: '';
        
        // Primary color (uit categorie)
        $primary_hex = 'D97706'; // Default fallback (zonder # of FF)
        $terms = get_the_terms($post_id, 'recept_categorie');
        if (!empty($terms) && !is_wp_error($terms)) {
            $term = array_shift($terms);
            $color = get_term_meta($term->term_id, 'rick_category_color', true);
            if ($color) {
                $primary_hex = ltrim($color, '#');
            }
        }

        // Ingrediënten splitten per regel
        $ingredienten_ruw = get_field('ingredienten', $post_id) ?: '';
        $ingredienten_lijst = [];
        if ($ingredienten_ruw) {
            $raw_regels = preg_split('/\r\n|\r|\n|<br\s*\/?>/', $ingredienten_ruw);
            foreach ($raw_regels as $regel) {
                $regel = wp_strip_all_tags(trim($regel));
                if ($regel === '') continue;
                $parts = explode('|', $regel, 2);
                $ingredienten_lijst[] = [
                    'naam'        => trim($parts[0] ?? ''),
                    'hoeveelheid' => trim($parts[1] ?? '')
                ];
            }
        }

        // Bereidingswijze splitten
        $bereidingswijze_ruw = wp_strip_all_tags(get_field('bereidingswijze', $post_id) ?: '');
        $stappen_lijst = [];
        if ($bereidingswijze_ruw) {
             $stappen = preg_split('/\r\n|\r|\n/', $bereidingswijze_ruw);
             foreach ($stappen as $stap) {
                 $stap = trim($stap);
                 if ($stap !== '') {
                     $stappen_lijst[] = $stap;
                 }
             }
        }

        $recepten_data[] = [
            'titel'           => get_the_title($post_id),
            'ondertitel'      => get_field('meta_info', $post_id) ?: '',
            'afbeelding'      => $afbeelding_url,
            'bereidingstijd'  => get_field('bereidingstijd', $post_id) ?: '',
            'intro'           => wp_strip_all_tags(get_field('intro_tekst', $post_id) ?: ''),
            'ingredienten'    => $ingredienten_lijst,
            'bereidingswijze' => $stappen_lijst,
            'bakker_tip'      => wp_strip_all_tags(get_field('bakker_tip', $post_id) ?: ''),
            'kleur'           => $primary_hex
        ];
    }

    // Haal baktips op
    $baktips = get_posts([
        'post_type'      => 'baktip',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    $baktips_data = [];
    foreach ($baktips as $tip) {
        $acf_tekst = get_field('baktip_tekst', $tip->ID);
        // Fallback naar the_content als acf leeg is (voor backwards compatibility)
        $content_ruw = wp_strip_all_tags($acf_tekst ? $acf_tekst : $tip->post_content);
        
        $baktips_data[] = [
            'titel'      => get_the_title($tip->ID),
            'content'    => $content_ruw,
            'afbeelding' => get_the_post_thumbnail_url($tip->ID, 'large') ?: ''
        ];
    }

    $recepten_count = count($recepten);
    $baktips_count = count($baktips);
    ?>
    <div class="wrap">
        <h1>📄 Recepten exporteren naar PowerPoint (via PptxGenJS)</h1>
        <p>Exporteert alle gepubliceerde recepten naar één PowerPoint-bestand. Het genereren gebeurt supersnel lokaal in je browser, inclusief perfecte auto-paginering!</p>

        <div style="margin-top: 30px;">
            <?php if ($recepten_count > 0 || $baktips_count > 0) : ?>
                <button id="rick-start-export" class="button button-primary button-hero">
                    ⬇️ Start Export (<?php echo $recepten_count; ?> recepten, <?php echo $baktips_count; ?> baktips)
                </button>
                <span id="rick-export-status" style="margin-left: 15px; font-style: italic; color: #666;"></span>
            <?php else : ?>
                <p><em>Geen gepubliceerde content gevonden om te exporteren.</em></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- PptxGenJS CDN direct in de view laden om hook-problemen te voorkomen -->
    <script src="https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.bundle.js"></script>

    <!-- Inline script voor de export logica -->
    <script>
    // Data direct beschikbaar maken
    window.RickPptData = {
        recepten: <?php echo json_encode($recepten_data); ?>,
        baktips: <?php echo json_encode($baktips_data); ?>
    };

    document.addEventListener("DOMContentLoaded", function() {
        var btn = document.getElementById("rick-start-export");
        if (!btn) return;

        btn.addEventListener("click", function() {
            var statusEl = document.getElementById("rick-export-status");
            statusEl.innerText = "PowerPoint wordt opgebouwd, een moment geduld aub...";
            btn.disabled = true;

            setTimeout(function() {
                genereerPowerPoint();
            }, 100); // kleine vertraging zodat de UI kan updaten
        });

        function genereerPowerPoint() {
            if (typeof PptxGenJS === 'undefined') {
                alert("PptxGenJS is niet geladen! Controleer je internetverbinding (CDN).");
                return;
            }

            var pptx = new PptxGenJS();
            
            // A4 Formaat / Custom layout (216x303mm = ca 8.5 x 11.93 inches)
            pptx.defineLayout({ name: "A4Plus", width: 8.5, height: 11.93 });
            pptx.layout = "A4Plus";
            
            // Recepten uitlezen
            var recepten = window.RickPptData.recepten;

            // Constanten & Kleuren
            var C_BG       = 'F9FAFB'; // body bg
            var C_CARD     = 'FFFFFF'; // container card bg
            var C_DARK     = '2C2C2C'; // text dark
            var C_MUTED    = '6B7280'; // text muted
            var C_BADGE_BG = 'FEF3C7'; // badge bg
            var C_BADGE_TX = '92400E'; // badge text
            var C_INTR_BG  = 'FFFBEB'; // intro bg
            var C_LINE     = 'E5E7EB'; // line separator
            var C_TIP_BG   = 'F0FDF4'; // tip bg
            var C_TIP_LINE = 'BBF7D0'; // tip border
            var C_TIP_TX   = '166534'; // tip titel text
            
            recepten.forEach(function(rec, index) {
                var PRIMARY = rec.kleur;

                // Definieer slide master per recept (voor auto-paging op dezelfde achtergrond)
                var masterName = "MASTER_RECEPT_" + index;
                
                var bkgObjects = [
                    { rect: { x:0, y:0, w:'100%', h:'100%', fill: { color: C_BG } } },
                    // Witte kaart
                    { rect: { x:0.39, y:0.39, w:7.72, h:11.15, fill: { color: C_CARD }, roundness: 0.1, line: { color:'E5E7EB', width:1 } } }
                ];
                
                pptx.defineSlideMaster({
                    title: masterName,
                    background: { color: C_BG },
                    objects: bkgObjects
                });

                var slide = pptx.addSlide({ masterName: masterName });

                // Huidige Y positie bijhouden (in inches)
                var currY = 0.8; 
                var marginX = 0.8;
                var contentW = 6.9; // 8.5 - 2*0.8

                // -- ONDERTITEL --
                if (rec.ondertitel) {
                    slide.addText(rec.ondertitel, {
                        x: marginX, y: currY, w: contentW * 0.5, h: 0.3,
                        fontFace: 'Segoe UI', fontSize: 11, color: C_MUTED, italic: true
                    });
                    currY += 0.3;
                }

                // -- TITEL --
                slide.addText(rec.titel, {
                    x: marginX, y: currY, w: contentW * 0.55, h: 0.8,
                    fontFace: 'Segoe UI', fontSize: 24, bold: true, color: PRIMARY, valign: 'top'
                });
                currY += 0.8;

                // -- BADGE (Bereidingstijd) --
                if (rec.bereidingstijd) {
                    slide.addText("⏱ " + rec.bereidingstijd, {
                        x: marginX, y: currY, w: 2.2, h: 0.3,
                        fontFace: 'Segoe UI', fontSize: 10, bold: true, color: C_BADGE_TX,
                        fill: { color: C_BADGE_BG }, align: 'center', roundness: 0.5
                    });
                    currY += 0.5;
                }

                var headerEndY = currY;

                // -- AFBEELDING --
                if (rec.afbeelding) {
                    var imgW = 2.8;
                    var imgH = 1.8;
                    slide.addImage({
                        path: rec.afbeelding,
                        x: marginX + contentW - imgW, 
                        y: 0.8, 
                        w: imgW, 
                        h: imgH,
                        sizing: { type: 'contain', w: imgW, h: imgH }
                    });
                    if ((0.8 + imgH + 0.2) > headerEndY) {
                        headerEndY = 0.8 + imgH + 0.2;
                    }
                }
                
                currY = headerEndY;

                // -- SEPARATOR --
                slide.addShape(pptx.ShapeType.line, {
                    x: marginX, y: currY, w: contentW, h: 0, line: { color: C_LINE, width: 1 }
                });
                currY += 0.2;

                // -- INTRO --
                if (rec.intro) {
                    var introH = 0.8;
                    // Linker border-blok
                    slide.addShape(pptx.ShapeType.rect, {
                        x: marginX, y: currY, w: 0.1, h: introH, fill: { color: PRIMARY }
                    });
                    // Rest bg
                    slide.addShape(pptx.ShapeType.rect, {
                        x: marginX + 0.1, y: currY, w: contentW - 0.1, h: introH, fill: { color: C_INTR_BG }
                    });
                    // Tekst
                    slide.addText(rec.intro, {
                        x: marginX + 0.2, y: currY + 0.1, w: contentW - 0.3, h: introH - 0.2,
                        fontFace: 'Segoe UI', fontSize: 10, color: C_DARK, valign: 'top'
                    });
                    currY += introH + 0.3;
                }

                // -- INGREDIËNTEN (Volledige breedte) --
                if (rec.ingredienten && rec.ingredienten.length > 0) {
                    slide.addText("Ingrediënten", {
                        x: marginX, y: currY, w: contentW, h: 0.4,
                        fontFace: 'Segoe UI', fontSize: 14, bold: true, color: '1F2937'
                    });
                    currY += 0.4;

                    var tableRows = [];
                    rec.ingredienten.forEach(function(ing, i) {
                        var bgc = (i % 2 === 0) ? 'FFF7ED' : 'FFFDF8';
                        tableRows.push([
                            { text: ing.naam, options: { fill:bgc, fontSize:10, color:C_DARK } },
                            { text: ing.hoeveelheid, options: { fill:bgc, fontSize:10, color:C_DARK } }
                        ]);
                    });

                    // We schatten de hoogte van de tabel om currY bij te werken
                    // 1 rij is ongeveer 0.25 inch
                    var estimatedTableH = tableRows.length * 0.25;

                    slide.addTable(tableRows, {
                        x: marginX,
                        y: currY,
                        w: contentW,
                        colW: [contentW * 0.7, contentW * 0.3],
                        margin: 0.05,
                        autoPage: false // We laten de tabel niet auto-pagen om currY controle te houden
                    });

                    currY += estimatedTableH + 0.4;
                }

                // -- BEREIDINGSWIJZE (Onder de ingrediënten) --
                if (rec.bereidingswijze && rec.bereidingswijze.length > 0) {
                    // Als currY te laag is (bijv. onder 8 inch), verplaatsen we bereiding naar een nieuwe slide
                    var targetSlide = slide;
                    var targetY = currY;

                    if (targetY > 9) {
                        targetSlide = pptx.addSlide({ masterName: masterName });
                        targetY = 0.8;
                    }

                    targetSlide.addText("Bereidingswijze", {
                        x: marginX, y: targetY, w: contentW, h: 0.4,
                        fontFace: 'Segoe UI', fontSize: 14, bold: true, color: '1F2937'
                    });
                    targetY += 0.4;

                    var textArr = [];
                    rec.bereidingswijze.forEach(function(stap) {
                        if (stap.indexOf('##') === 0) {
                            var kop = stap.replace('##', '').trim();
                            textArr.push({ text: kop + "\n", options: { bold:true, color:PRIMARY, fontSize: 12, breakLine: true } });
                        } else {
                            textArr.push({ text: "• " + stap + "\n\n", options: { color:C_DARK, fontSize: 10, breakLine: true } });
                        }
                    });

                    targetSlide.addText(textArr, {
                        x: marginX, y: targetY, w: contentW, h: 10.5 - targetY,
                        valign: "top",
                        autoPage: true // Paginering voor stappen!
                    });
                }

                // -- BAKKER TIP (Op aparte slide als afsluiter van het recept) --
                if (rec.bakker_tip) {
                    var tipSlide = pptx.addSlide({ masterName: masterName });
                    
                    tipSlide.addShape(pptx.ShapeType.rect, {
                        x: marginX, y: 0.8, w: contentW, h: 1.5,
                        fill: { color: C_TIP_BG }, line: { color: C_TIP_LINE, width:1 }, roundness: 0.1
                    });

                    tipSlide.addText("💡 Tip van de bakker:\n", {
                        x: marginX + 0.2, y: 0.9, w: contentW - 0.4, h: 0.3,
                        fontFace: 'Segoe UI', fontSize: 11, bold: true, color: C_TIP_TX, valign:'top'
                    });

                    tipSlide.addText(rec.bakker_tip, {
                        x: marginX + 0.2, y: 1.3, w: contentW - 0.4, h: 0.8,
                        fontFace: 'Segoe UI', fontSize: 10, italic: true, color: C_DARK, valign:'top'
                    });
                }

            }); // end forEach recept

            // -- HANDIGE BAKTIPS (Achterin het boek) --
            var baktips = window.RickPptData.baktips;
            if (baktips && baktips.length > 0) {
                // Introductie slide voor baktips
                var tipIntroSlide = pptx.addSlide();
                tipIntroSlide.background = { color: 'F9FAFB' };
                tipIntroSlide.addText("Handige Baktips", {
                    x: '10%', y: '40%', w: '80%', h: 1,
                    fontFace: 'Segoe UI', fontSize: 36, bold: true, color: 'D97706', align: 'center'
                });

                // Master voor baktips
                var tipMasterName = "MASTER_BAKTIP";
                pptx.defineSlideMaster({
                    title: tipMasterName,
                    background: { color: C_BG },
                    objects: [
                        { rect: { x:0.39, y:0.39, w:7.72, h:11.15, fill: { color: C_CARD }, roundness: 0.1, line: { color:'E5E7EB', width:1 } } },
                        // Kleine decoratieve header
                        { rect: { x:0.39, y:0.39, w:7.72, h:0.5, fill: { color: 'FEF3C7' }, roundness: 0.1 } },
                        { text: { text: "💡 Handige Baktip", options: { x:0.5, y:0.45, w:3, h:0.4, fontFace:'Segoe UI', fontSize:14, bold:true, color:'D97706' } } }
                    ]
                });

                baktips.forEach(function(tip) {
                    var slide = pptx.addSlide({ masterName: tipMasterName });
                    var tY = 1.2;

                    slide.addText(tip.titel, {
                        x: 0.8, y: tY, w: 6.9, h: 0.8,
                        fontFace: 'Segoe UI', fontSize: 24, bold: true, color: '2C2C2C', valign: 'top'
                    });
                    tY += 0.8;

                    if (tip.afbeelding) {
                        slide.addImage({
                            path: tip.afbeelding,
                            x: 0.8, y: tY, w: 6.9, h: 3.5,
                            sizing: { type: 'contain', w: 6.9, h: 3.5 }
                        });
                        tY += 3.8;
                    }

                    if (tip.content) {
                        slide.addText(tip.content, {
                            x: 0.8, y: tY, w: 6.9, h: 11 - tY,
                            fontFace: 'Segoe UI', fontSize: 12, color: '2C2C2C', valign: 'top',
                            autoPage: true
                        });
                    }
                });
            }

            // Save
            var date = new Date();
            var datumString = date.getFullYear() + "-" + (date.getMonth() + 1).toString().padStart(2, '0') + "-" + date.getDate().toString().padStart(2, '0');
            pptx.writeFile({ fileName: "Recepten-Export-" + datumString + ".pptx" }).then(function() {
                var statusEl = document.getElementById("rick-export-status");
                statusEl.innerText = "✅ Export succesvol gedownload!";
                document.getElementById("rick-start-export").disabled = false;
            }).catch(function(err) {
                alert("Fout bij genereren PowerPoint: " + err);
                document.getElementById("rick-start-export").disabled = false;
            });
        }
    });
    </script>
    <?php
}

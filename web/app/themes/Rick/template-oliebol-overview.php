<?php
/*
Template Name: Overview oliebollen
Description: Pagina-template voor een dynamisch overzicht van alle beoordeelde oliebollenlocaties met sortering en score.
*/

get_header();

$sort_option = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'date';
$search_query = isset($_GET['s_oliebol']) ? sanitize_text_field($_GET['s_oliebol']) : '';

$query_args = array(
    'post_type'      => 'oliebol',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
);

if ( ! empty($search_query) ) {
    $query_args['s'] = $search_query;
}

$query = new WP_Query($query_args);
$oliebol_posts = $query->posts;

// Sorteren
if ($sort_option === 'stars') {
    usort($oliebol_posts, function($a, $b) {
        $cijfer_a = (float)(function_exists('get_field') ? get_field('oliebol_cijfer', $a->ID) : get_post_meta($a->ID, 'oliebol_cijfer', true));
        $cijfer_b = (float)(function_exists('get_field') ? get_field('oliebol_cijfer', $b->ID) : get_post_meta($b->ID, 'oliebol_cijfer', true));
        if ($cijfer_a === $cijfer_b) {
            return strcmp($b->post_date, $a->post_date);
        }
        return $cijfer_b <=> $cijfer_a;
    });
} elseif ($sort_option === 'location') {
    usort($oliebol_posts, function($a, $b) {
        $loc_a = (string)(function_exists('get_field') ? get_field('oliebol_locatie', $a->ID) : get_post_meta($a->ID, 'oliebol_locatie', true));
        $loc_b = (string)(function_exists('get_field') ? get_field('oliebol_locatie', $b->ID) : get_post_meta($b->ID, 'oliebol_locatie', true));
        if (empty($loc_a)) $loc_a = $a->post_title;
        if (empty($loc_b)) $loc_b = $b->post_title;
        return strcasecmp($loc_a, $loc_b);
    });
} else {
    usort($oliebol_posts, function($a, $b) {
        return strcmp($b->post_date, $a->post_date);
    });
}
?>

<style>
    .section-overview-oliebol {
        padding: 40px 0 70px;
        background: #fdfaf5;
        min-height: 80vh;
    }

    .section-overview-oliebol .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .section-overview-oliebol .intro {
        text-align: center;
        margin-bottom: 35px;
    }

    .section-overview-oliebol .intro-badge {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 12px;
        border: 1px solid #fde68a;
    }

    .section-overview-oliebol .intro h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #381e11;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .section-overview-oliebol .intro-sub {
        font-size: 1.05rem;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto 24px;
    }

    /* Zoekbalk & Sorteerknoppen */
    .section-overview-oliebol .controls-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin-bottom: 35px;
    }

    .section-overview-oliebol .search-form {
        display: flex;
        width: 100%;
        max-width: 500px;
        gap: 8px;
    }

    .section-overview-oliebol .search-input {
        flex: 1;
        padding: 11px 16px;
        border: 1.5px solid #d1d5db;
        border-radius: 12px;
        font-size: 0.95rem;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .section-overview-oliebol .search-input:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    }

    .section-overview-oliebol .search-button {
        padding: 11px 20px;
        background: #d97706;
        color: #ffffff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .section-overview-oliebol .search-button:hover {
        background: #b45309;
    }

    .section-overview-oliebol .sort-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .section-overview-oliebol .sort-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 999px;
        border: 1.5px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
        font-weight: 600;
        font-size: 0.88rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .section-overview-oliebol .sort-btn:hover {
        border-color: #d97706;
        color: #d97706;
    }

    .section-overview-oliebol .sort-btn.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        border-color: #d97706;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
    }

    /* Kaarten Grid */
    .section-overview-oliebol .card-holder {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: flex-start;
    }

    .section-overview-oliebol .card {
        flex: 1 1 calc(33.333% - 24px);
        min-width: 290px;
        background-color: #ffffff;
        border-radius: 18px;
        border: 1.5px solid #f3e8d6;
        padding: 24px;
        box-sizing: border-box;
        box-shadow: 0 4px 14px rgba(56, 30, 17, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .section-overview-oliebol .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(56, 30, 17, 0.12);
        border-color: #f59e0b;
    }

    .section-overview-oliebol .card-topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 12px;
    }

    .section-overview-oliebol .card-topbar .title h3 {
        font-size: 1.22rem;
        font-weight: 800;
        color: #381e11;
        margin: 0 0 6px 0;
        line-height: 1.3;
    }

    .section-overview-oliebol .card-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }

    .section-overview-oliebol .tag {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .section-overview-oliebol .tag--locatie {
        background: #fef3c7;
        color: #92400e;
    }

    .section-overview-oliebol .tag--kraam {
        background: #e0f2fe;
        color: #0369a1;
    }

    .section-overview-oliebol .tag--prijs {
        background: #ecfdf5;
        color: #065f46;
    }

    .section-overview-oliebol .tag--smaak {
        background: #fdf2f8;
        color: #9d174d;
        border-color: #fbcfe8;
    }

    /* Sterren & Cijfer */
    .section-overview-oliebol .stars-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        flex-shrink: 0;
    }

    .section-overview-oliebol .stars {
        color: #f59e0b;
        font-size: 1.15rem;
        letter-spacing: 1px;
        white-space: nowrap;
        line-height: 1;
    }

    .section-overview-oliebol .star--empty {
        color: #e5e7eb;
    }

    .section-overview-oliebol .score-number {
        font-size: 0.85rem;
        font-weight: 800;
        color: #92400e;
        background: #fffbeb;
        padding: 2px 7px;
        border-radius: 6px;
        margin-top: 4px;
        border: 1px solid #fde68a;
    }

    /* Omschrijving */
    .section-overview-oliebol .card-description {
        font-size: 0.94rem;
        color: #4b5563;
        line-height: 1.5;
        margin: 12px 0 18px;
        flex-grow: 1;
    }

    /* Kaart Endbar */
    .section-overview-oliebol .card-endbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f3f4f6;
        padding-top: 12px;
        margin-top: auto;
        font-size: 0.82rem;
        color: #6b7280;
    }

    .section-overview-oliebol .card-endbar .author {
        font-weight: 600;
        color: #381e11;
    }

    .section-overview-oliebol .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px dashed #d1d5db;
        max-width: 600px;
        margin: 0 auto;
    }

    .section-overview-oliebol .empty-state h3 {
        color: #381e11;
        margin-bottom: 8px;
    }

    /* Responsief */
    @media (max-width: 992px) {
        .section-overview-oliebol .card {
            flex: 1 1 calc(50% - 24px);
        }
    }

    @media (max-width: 640px) {
        .section-overview-oliebol .card {
            flex: 1 1 100%;
        }
        .section-overview-oliebol .intro h1 {
            font-size: 1.7rem;
        }
    }
</style>

<main class="section-overview-oliebol">
    <div class="container">
        <div class="intro">
            <span class="intro-badge">✨ Oliebollen Test & Beoordelingen</span>
            <h1>Overzicht van ingevulde locaties</h1>
            <p class="intro-sub">Bekijk alle beoordeelde oliebollenkramen en bakkers. Sorteer direct op datum, meeste sterren of plaatsnaam.</p>

            <div class="controls-wrapper">
                <!-- Zoekbalk -->
                <form method="GET" action="" class="search-form">
                    <?php if ( ! empty($sort_option) && $sort_option !== 'date' ) : ?>
                        <input type="hidden" name="sort" value="<?php echo esc_attr($sort_option); ?>" />
                    <?php endif; ?>
                    <input type="text" name="s_oliebol" value="<?php echo esc_attr($search_query); ?>" placeholder="Zoek op plaats, kraam of bakker..." class="search-input" />
                    <button type="submit" class="search-button">Zoeken</button>
                </form>

                <!-- Sorteerknoppen -->
                <div class="sort-buttons">
                    <?php
                    $base_url = remove_query_arg('sort');
                    ?>
                    <a href="<?php echo esc_url(add_query_arg('sort', 'date', $base_url)); ?>" class="sort-btn <?php echo $sort_option === 'date' ? 'active' : ''; ?>">
                        <span>📅</span> Sorteer op datum
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('sort', 'stars', $base_url)); ?>" class="sort-btn <?php echo $sort_option === 'stars' ? 'active' : ''; ?>">
                        <span>⭐</span> Sorteer op meeste sterren
                    </a>
                    <a href="<?php echo esc_url(add_query_arg('sort', 'location', $base_url)); ?>" class="sort-btn <?php echo $sort_option === 'location' ? 'active' : ''; ?>">
                        <span>📍</span> Sorteer op plaats
                    </a>
                </div>
            </div>
        </div>

        <div class="card-holder">
            <?php if ( ! empty($oliebol_posts) ) : ?>
                <?php foreach ( $oliebol_posts as $post ) : setup_postdata($post);
                    $post_id    = get_the_ID();
                    $locatie    = function_exists('get_field') ? get_field('oliebol_locatie', $post_id) : get_post_meta($post_id, 'oliebol_locatie', true);
                    $kraam      = function_exists('get_field') ? get_field('oliebol_kraam', $post_id) : get_post_meta($post_id, 'oliebol_kraam', true);
                    $smaak      = function_exists('get_field') ? get_field('oliebol_smaak', $post_id) : get_post_meta($post_id, 'oliebol_smaak', true);
                    $auteur     = function_exists('get_field') ? get_field('oliebol_auteur', $post_id) : get_post_meta($post_id, 'oliebol_auteur', true);
                    $cijfer_raw = function_exists('get_field') ? get_field('oliebol_cijfer', $post_id) : get_post_meta($post_id, 'oliebol_cijfer', true);
                    $prijs      = function_exists('get_field') ? get_field('oliebol_prijs', $post_id) : get_post_meta($post_id, 'oliebol_prijs', true);
                    $omschrijving = function_exists('get_field') ? get_field('oliebol_omschrijving', $post_id) : get_post_meta($post_id, 'oliebol_omschrijving', true);

                    if ( empty($omschrijving) ) {
                        $omschrijving = get_the_excerpt() ?: wp_trim_words(get_the_content(), 25, '...');
                    }

                    $cijfer = floatval($cijfer_raw);
                    // 5-sterren berekening
                    $volle_sterren = (int) floor($cijfer / 2);
                    $halve_sterren = (($cijfer / 2) - $volle_sterren >= 0.4) ? 1 : 0;
                    $lege_sterren  = max(0, 5 - $volle_sterren - $halve_sterren);
                ?>

                    <div class="card" id="card-<?php echo esc_attr($post_id); ?>">
                        <div>
                            <div class="card-topbar">
                                <div class="title">
                                    <h3>
                                        <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>
                                    <div class="card-tags">
                                        <?php if ( ! empty($locatie) ) : ?>
                                            <span class="tag tag--locatie">📍 <?php echo esc_html($locatie); ?></span>
                                        <?php endif; ?>
                                        <?php if ( ! empty($smaak) ) : ?>
                                            <span class="tag tag--smaak">🥐 <?php echo esc_html($smaak); ?></span>
                                        <?php endif; ?>
                                        <?php if ( ! empty($kraam) ) : ?>
                                            <span class="tag tag--kraam">🎪 <?php echo esc_html($kraam); ?></span>
                                        <?php endif; ?>
                                        <?php if ( ! empty($prijs) ) : ?>
                                            <span class="tag tag--prijs">💶 €<?php echo esc_html(str_replace('€', '', $prijs)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="stars-wrapper">
                                    <div class="stars" title="<?php echo esc_attr($cijfer); ?>/10 sterren">
                                        <?php
                                        for ($i = 0; $i < $volle_sterren; $i++) {
                                            echo '<span class="star star--full">★</span>';
                                        }
                                        if ($halve_sterren) {
                                            echo '<span class="star star--half">★</span>';
                                        }
                                        for ($i = 0; $i < $lege_sterren; $i++) {
                                            echo '<span class="star star--empty">☆</span>';
                                        }
                                        ?>
                                    </div>
                                    <?php if ( $cijfer_raw !== '' && $cijfer_raw !== null ) : ?>
                                        <span class="score-number"><?php echo esc_html(number_format($cijfer, 1, ',', '')); ?> / 10</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="card-description"><?php echo esc_html($omschrijving); ?></p>
                        </div>

                        <div class="card-endbar">
                            <div class="date">
                                <small>Ingevuld op: <?php echo esc_html(get_the_date('j-n-Y H:i')); ?></small>
                            </div>
                            <?php if ( ! empty($auteur) ) : ?>
                                <div class="author">
                                    <small>✍️ <?php echo esc_html($auteur); ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endforeach; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="empty-state">
                    <h3>🥐 Nog geen oliebollen beoordeeld</h3>
                    <p style="color:#6b7280;margin-bottom:0;">Er zijn momenteel geen oliebollenlocaties gevonden. Voeg er direct een toe via het inzendformulier!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();

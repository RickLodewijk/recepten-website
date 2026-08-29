<?php
/**
 * Archive template voor Recepten (CPT recept).
 * Wordt geladen op /recepten/ en taxonomie-archieven.
 */

get_header();

$search_query = isset($_GET['s_recept']) ? sanitize_text_field($_GET['s_recept']) : (isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '');
$category_filter = isset($_GET['cat_recept']) ? sanitize_text_field($_GET['cat_recept']) : '';

if (is_tax('recept_categorie')) {
    $current_term = get_queried_object();
    if ($current_term && !empty($current_term->slug)) {
        $category_filter = $current_term->slug;
    }
}

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type'      => 'recept',
    'posts_per_page' => 24,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

if ($search_query) {
    $args['s'] = $search_query;
}

if ($category_filter) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'recept_categorie',
            'field'    => 'slug',
            'terms'    => $category_filter,
        ),
    );
}

$query = new WP_Query($args);
?>

<!-- Hero & Filter Sectie -->
<section class="home-hero">
    <div class="container">
        <h2>Ontdek heerlijke recepten</h2>
        <p style="color: #6b7280; max-width: 600px; margin: -15px auto 25px auto; font-size: 1.05rem;">
            Verse, huisgemaakte gerechten en baksels met stap-voor-stap uitleg en handige ingrediëntenlijsten.
        </p>

        <form class="recipe-filters" method="GET" action="<?php echo esc_url(get_post_type_archive_link('recept')); ?>">
            <div class="filter-group search-group">
                <input type="text" name="s_recept" placeholder="Zoek op ingrediënt of gerecht..." value="<?php echo esc_attr($search_query); ?>">
            </div>
            <div class="filter-group category-group">
                <select name="cat_recept" onchange="this.form.submit()">
                    <option value="">Alle categorieën</option>
                    <?php
                    $categories = get_terms(array('taxonomy' => 'recept_categorie', 'hide_empty' => false));
                    if (!empty($categories) && !is_wp_error($categories)) :
                        foreach ($categories as $cat) {
                            $selected = ($category_filter === $cat->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($cat->slug) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
                        }
                    endif;
                    ?>
                </select>
            </div>
            <button type="submit" class="button">Zoeken</button>
            <?php if ($search_query || $category_filter) : ?>
                <div style="width: 100%; margin-top: 12px;">
                    <a href="<?php echo esc_url(get_post_type_archive_link('recept')); ?>" class="reset-link">✕ Reset filters</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Recepten Grid -->
<section class="recipe-grid-section">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <p style="color: #6b7280; font-size: 0.95rem; margin: 0;">
                <strong><?php echo intval($query->found_posts); ?></strong> <?php echo $query->found_posts === 1 ? 'recept' : 'recepten'; ?> gevonden
            </p>
        </div>

        <?php if ($query->have_posts()) : ?>
            <div class="recipe-grid">
                <?php while ($query->have_posts()) : $query->the_post(); 
                    $post_id = get_the_ID();
                    $primary_cat = function_exists('rick_get_recept_primary_category') ? rick_get_recept_primary_category($post_id) : null;
                    $cat_color = function_exists('rick_get_recept_primary_category_color') ? rick_get_recept_primary_category_color($post_id) : '#d97706';
                    $bereidingstijd = function_exists('rick_get_recept_field') ? rick_get_recept_field('bereidingstijd', $post_id) : get_post_meta($post_id, 'bereidingstijd', true);
                    $moeilijkheid = function_exists('rick_get_recept_field') ? rick_get_recept_field('moeilijkheidsgraad', $post_id) : get_post_meta($post_id, 'moeilijkheidsgraad', true);
                ?>
                    <a href="<?php the_permalink(); ?>" class="recipe-card">
                        <div class="recipe-card__image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <div class="placeholder-image" style="background: linear-gradient(135deg, <?php echo esc_attr($cat_color); ?> 0%, #78350f 100%);">
                                    <span>🍳</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="recipe-card__content">
                            <?php if ($primary_cat) : ?>
                                <span class="recipe-card__category" style="background-color: <?php echo esc_attr($cat_color); ?>">
                                    <?php echo esc_html($primary_cat->name); ?>
                                </span>
                            <?php endif; ?>

                            <h3 class="recipe-card__title"><?php the_title(); ?></h3>

                            <?php if ($bereidingstijd || $moeilijkheid) : ?>
                                <div style="display: flex; gap: 12px; margin-top: 10px; font-size: 0.82rem; color: #6b7280;">
                                    <?php if ($bereidingstijd) : ?>
                                        <span>⏱️ <?php echo esc_html($bereidingstijd); ?></span>
                                    <?php endif; ?>
                                    <?php if ($moeilijkheid) : ?>
                                        <span>📊 <?php echo esc_html($moeilijkheid); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <!-- Paginering -->
            <?php if ($query->max_num_pages > 1) : ?>
                <div style="margin-top: 40px; text-align: center;">
                    <?php
                    echo paginate_links(array(
                        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, get_query_var('paged')),
                        'total'     => $query->max_num_pages,
                        'prev_text' => '← Vorige',
                        'next_text' => 'Volgende →',
                    ));
                    ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <div style="text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb;">
                <p style="font-size: 2.5rem; margin: 0 0 10px 0;">🔍</p>
                <h3 style="color: #92400e; margin: 0 0 8px 0;">Geen recepten gevonden</h3>
                <p style="color: #6b7280; margin: 0 0 20px 0;">Er zijn geen recepten die voldoen aan je zoekcriteria.</p>
                <a href="<?php echo esc_url(get_post_type_archive_link('recept')); ?>" class="button" style="display: inline-block; padding: 10px 22px; background: #d97706; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 700;">
                    Toon alle recepten
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

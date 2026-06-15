<?php
get_header();

$search_query = isset($_GET['s_recept']) ? sanitize_text_field($_GET['s_recept']) : '';
$category_filter = isset($_GET['cat_recept']) ? sanitize_text_field($_GET['cat_recept']) : '';

$args = array(
    'post_type' => 'recept',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
);

if ($search_query) {
    $args['s'] = $search_query;
}

if ($category_filter) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'recept_categorie',
            'field' => 'slug',
            'terms' => $category_filter,
        ),
    );
}

$query = new WP_Query($args);
?>

<section class="home-hero">
    <div class="container">
        <h2>Ontdek heerlijke recepten</h2>
        <form class="recipe-filters" method="GET" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="filter-group search-group">
                <input type="text" name="s_recept" placeholder="Zoek een recept..." value="<?php echo esc_attr($search_query); ?>">
            </div>
            <div class="filter-group category-group">
                <select name="cat_recept" onchange="this.form.submit()">
                    <option value="">Alle categorieën</option>
                    <?php
                    $categories = get_terms(array('taxonomy' => 'recept_categorie', 'hide_empty' => false));
                    if (!empty($categories) && !is_wp_error($categories)) :
                        foreach ($categories as $cat) {
                            $selected = ($category_filter == $cat->slug) ? 'selected' : '';
                            echo '<option value="' . esc_attr($cat->slug) . '" ' . $selected . '>' . esc_html($cat->name) . '</option>';
                        }
                    endif;
                    ?>
                </select>
            </div>
            <button type="submit" class="button">Zoeken</button>
            <?php if ($search_query || $category_filter) : ?>
                <div style="width: 100%; margin-top: 10px;">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="reset-link">Reset filters</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<section class="recipe-grid-section">
    <div class="container">
        <?php if ($query->have_posts()) : ?>
            <div class="recipe-grid">
                <?php while ($query->have_posts()) : $query->the_post(); 
                    $primary_cat = rick_get_recept_primary_category();
                    $cat_color = rick_get_recept_primary_category_color();
                ?>
                    <a href="<?php the_permalink(); ?>" class="recipe-card">
                        <div class="recipe-card__image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php else : ?>
                                <div class="placeholder-image" style="background-color: <?php echo esc_attr($cat_color); ?>">
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
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="no-results">Geen recepten gevonden die voldoen aan je zoekcriteria.</p>
        <?php endif; ?>
    </div>
</section>

<?php
get_footer();

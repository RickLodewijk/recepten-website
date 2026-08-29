</main>

<footer class="site-footer">
    <div class="container site-footer__container">
        <div class="site-footer__grid">
            <!-- Kolom 1: Merk & Tagline -->
            <div class="site-footer__col site-footer__col--brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__brand-link">
                    <span class="site-footer__brand-icon">🍳</span>
                    <span class="site-footer__brand-name"><?php bloginfo('name'); ?></span>
                </a>
                <p class="site-footer__tagline">
                    De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.
                </p>
                <div class="site-footer__badge">
                    <span>👨‍🍳 Huisgemaakt & Verse Recepten</span>
                </div>
            </div>

            <!-- Kolom 2: Snelle Navigatie Links -->
            <div class="site-footer__col">
                <h3 class="site-footer__heading">Navigatie</h3>
                <ul class="site-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('recept') ?: home_url('/recepten/')); ?>">Alle Recepten</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('baktip') ?: home_url('/baktips/')); ?>">Baktips</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('pepernoot') ?: home_url('/pepernoten/')); ?>">Pepernoten</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Recept Categorieën -->
            <div class="site-footer__col">
                <h3 class="site-footer__heading">Categorieën</h3>
                <ul class="site-footer__links">
                    <?php
                    $categories = get_terms([
                        'taxonomy' => 'recept_categorie',
                        'hide_empty' => false,
                        'number' => 6,
                    ]);
                    if (!empty($categories) && !is_wp_error($categories)) :
                        foreach ($categories as $cat) :
                            $cat_link = get_term_link($cat);
                    ?>
                        <li><a href="<?php echo esc_url($cat_link); ?>"><?php echo esc_html($cat->name); ?></a></li>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Ontbijt</a></li>
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Avondeten</a></li>
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Snacks & Wraps</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Kolom 4: Bakker Tip & Scroll Top -->
            <div class="site-footer__col site-footer__col--tip">
                <h3 class="site-footer__heading">💡 Baktip</h3>
                <div class="site-footer__tip-box">
                    <p>
                        "Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!"
                    </p>
                </div>
                <a href="#top" class="site-footer__scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                    <span class="site-footer__scroll-arrow">&uarr;</span> Naar boven
                </a>
            </div>
        </div>

        <!-- Onderste balk: Copyright -->
        <div class="site-footer__bottom">
            <p class="site-footer__copyright">
                &copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. Alle rechten voorbehouden.
            </p>
            <p class="site-footer__credit">
                Gemaakt voor kook- en bakliefhebbers 🥐
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

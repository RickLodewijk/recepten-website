</main>

<?php
// Haal instelbare footer opties op
$brand_name   = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_brand_name', get_bloginfo('name')) : get_bloginfo('name');
$brand_icon   = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_brand_icon', '🍳') : '🍳';
$tagline      = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_tagline', 'De lekkerste recepten, baktips en kookinspiratie. Fresh, simpel en lekker bereid met liefde voor goed eten.') : 'De lekkerste recepten, baktips en kookinspiratie.';
$badge        = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_badge', '👨‍🍳 Huisgemaakt & Verse Recepten') : '👨‍🍳 Huisgemaakt & Verse Recepten';
$tip_heading  = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_tip_heading', '💡 Baktip') : '💡 Baktip';
$tip_text     = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_tip_text', 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!') : 'Laat deeg altijd op een tochtvrije, warme plek rijzen voor het allerbeste en meest luchtige resultaat!';
$copyright_raw = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_copyright', '© {year} ' . get_bloginfo('name') . '. Alle rechten voorbehouden.') : '© ' . date('Y') . ' ' . get_bloginfo('name') . '. Alle rechten voorbehouden.';
$copyright    = str_replace('{year}', date('Y'), $copyright_raw);
$credit       = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_credit', 'Gemaakt voor kook- en bakliefhebbers 🥐') : 'Gemaakt voor kook- en bakliefhebbers 🥐';
$email        = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_email', '') : '';
$instagram    = function_exists('rick_get_footer_setting') ? rick_get_footer_setting('footer_instagram', '') : '';
?>

<footer class="site-footer">
    <div class="container site-footer__container">
        <div class="site-footer__grid">
            <!-- Kolom 1: Merk & Tagline -->
            <div class="site-footer__col site-footer__col--brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__brand-link">
                    <?php if (!empty($brand_icon)) : ?>
                        <span class="site-footer__brand-icon"><?php echo esc_html($brand_icon); ?></span>
                    <?php endif; ?>
                    <span class="site-footer__brand-name"><?php echo esc_html($brand_name); ?></span>
                </a>

                <?php if (!empty($tagline)) : ?>
                    <p class="site-footer__tagline">
                        <?php echo esc_html($tagline); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($badge)) : ?>
                    <div class="site-footer__badge">
                        <span><?php echo esc_html($badge); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($email) || !empty($instagram)) : ?>
                    <div style="display: flex; gap: 12px; margin-top: 14px; font-size: 0.9rem;">
                        <?php if (!empty($email)) : ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" style="color: #92400e; text-decoration: none; font-weight: 600;">✉️ E-mail</a>
                        <?php endif; ?>
                        <?php if (!empty($instagram)) : ?>
                            <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" style="color: #92400e; text-decoration: none; font-weight: 600;">📸 Instagram</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Kolom 2: Snelle Navigatie Links -->
            <div class="site-footer__col">
                <h3 class="site-footer__heading">Navigatie</h3>
                <ul class="site-footer__links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('recept') ?: home_url('/recepten/')); ?>">Alle Recepten</a></li>
                    <li><a href="<?php echo esc_url(home_url('/pepernoot-registreren/')); ?>">Pepernoot Beoordelen</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('pepernoot') ?: home_url('/pepernoten/')); ?>">Pepernoten Test</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('baktip') ?: home_url('/baktips/')); ?>">Baktips</a></li>
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
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Avondeten</a></li>
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Drinken</a></li>
                        <li><a href="<?php echo esc_url(home_url('/recepten/')); ?>">Gebak</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Kolom 4: Bakker Tip & Scroll Top -->
            <div class="site-footer__col site-footer__col--tip">
                <?php if (!empty($tip_heading)) : ?>
                    <h3 class="site-footer__heading"><?php echo esc_html($tip_heading); ?></h3>
                <?php endif; ?>

                <?php if (!empty($tip_text)) : ?>
                    <div class="site-footer__tip-box">
                        <p><?php echo esc_html($tip_text); ?></p>
                    </div>
                <?php endif; ?>

                <a href="#top" class="site-footer__scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                    <span class="site-footer__scroll-arrow">&uarr;</span> Naar boven
                </a>
            </div>
        </div>

        <!-- Onderste balk: Copyright -->
        <div class="site-footer__bottom">
            <p class="site-footer__copyright">
                <?php echo esc_html($copyright); ?>
            </p>
            <?php if (!empty($credit)) : ?>
                <p class="site-footer__credit">
                    <?php echo esc_html($credit); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

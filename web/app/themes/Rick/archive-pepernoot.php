<?php
get_header();
?>
<head>
  <style>
    /* =========================================
       1. DESIGN TOKENS / THEMA VARIABELEN
       ========================================= */
    :root {
      --pn-bg: #F4ECE1;
      --pn-card-bg: #FFFDF9;
      --pn-dark: #381E11;
      --pn-medium: #5C321E;
      --pn-light: #8C532B;
      --pn-border: #E2D3BE;
      --pn-accent: #C7A779;
      --pn-text-main: #2A170E;
      --pn-text-muted: #6E5344;
      --pn-gold-start: #E68A00;
      --pn-gold-end: #A45500;
      --pn-pro-bg: #E8F5E9;
      --pn-pro-text: #1B5E20;
      --pn-pro-line: #2E7D32;
      --pn-con-bg: #FFEBEE;
      --pn-con-text: #B71C1C;
      --pn-con-line: #C62828;
      --pn-price-bg: #E0F2E9;
      --pn-price-text: #15693B;
      --pn-shadow-warm: 0 4px 14px rgba(56, 30, 17, 0.08);
      --pn-shadow-inset: inset 0 2px 4px rgba(56, 30, 17, 0.06);
    }

    /* =========================================
       2. RESET & BASIS STYLES
       ========================================= */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    body {
      background-color: var(--pn-bg);
      background-image: 
        radial-gradient(var(--pn-accent) 0.65px, transparent 0.65px),
        radial-gradient(var(--pn-accent) 0.65px, var(--pn-bg) 0.65px);
      background-size: 26px 26px;
      background-position: 0 0, 13px 13px;
      color: var(--pn-text-main);
      padding-bottom: 50px;
      line-height: 1.5;
    }

    /* =========================================
       3. HEADER & BRANDING
       ========================================= */
    .pn-header {
      background: #FFFFFF;
      border-bottom: 2px solid var(--pn-border);
      padding: 16px 20px;
      position: sticky;
      top: 0;
      z-index: 20;
      box-shadow: var(--pn-shadow-warm);
    }

    .pn-header__inner {
      max-width: 680px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .pn-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 900;
      font-size: 1.25rem;
      color: var(--pn-dark);
      letter-spacing: -0.5px;
    }

    .pn-brand__icon {
      width: 38px;
      height: 38px;
      background: linear-gradient(135deg, var(--pn-light), var(--pn-dark));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      box-shadow: 0 2px 5px rgba(56, 30, 17, 0.3), inset 0 1px 2px rgba(255, 255, 255, 0.4);
      transform: rotate(-8deg);
    }

    .pn-brand__subtitle {
      font-size: 11px;
      font-weight: 700;
      color: var(--pn-light);
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    /* =========================================
       4. LAYOUT & CONTROLS
       ========================================= */
    .pn-container {
      max-width: 680px;
      margin: 0 auto;
      padding: 20px 16px;
    }

    .pn-controls {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 20px;
    }

    @media (min-width: 480px) {
      .pn-controls {
        flex-direction: row;
      }
    }

    .pn-input,
    .pn-select {
      padding: 12px 16px;
      border: 2px solid var(--pn-border);
      border-radius: 14px;
      background: var(--pn-card-bg);
      font-size: 14px;
      color: var(--pn-text-main);
      outline: none;
      box-shadow: var(--pn-shadow-inset);
      transition: all 0.2s ease;
      width: 100%;
    }

    .pn-input:focus,
    .pn-select:focus {
      border-color: var(--pn-medium);
      background: #FFFFFF;
    }

    /* =========================================
       5. KAARTEN & REVIEW ITEMS
       ========================================= */
    .pn-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .pn-card {
      background: var(--pn-card-bg);
      border: 1.5px solid var(--pn-border);
      border-radius: 20px;
      padding: 18px 20px;
      box-shadow: var(--pn-shadow-warm);
      position: relative;
      overflow: hidden;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .pn-card:active {
      transform: scale(0.99);
    }

    .pn-card--top {
      border-color: var(--pn-accent);
      background: linear-gradient(180deg, #FFFCF5 0%, #FFF9EB 100%);
    }

    .pn-card__header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 14px;
    }

    .pn-card__title {
      font-size: 17px;
      font-weight: 800;
      color: var(--pn-dark);
      letter-spacing: -0.2px;
      margin-bottom: 4px;
      display: block;
    }

    .pn-card__tags {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
    }

    .pn-tag {
      font-size: 11px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 8px;
      border: 1px solid rgba(0,0,0,0.05);
    }

    .pn-tag--brand {
      background: #EFE4D6;
      color: var(--pn-medium);
    }

    .pn-tag--shop {
      background: #EAE1D2;
      color: var(--pn-text-muted);
    }

    .pn-tag--price {
      background: var(--pn-price-bg);
      color: var(--pn-price-text);
      font-weight: 800;
    }

    .pn-card__desc {
      font-size: 13.5px;
      color: var(--pn-text-muted);
      line-height: 1.45;
    }

    /* Pepernoot Score Badge */
    .pn-score {
      min-width: 58px;
      height: 58px;
      background: radial-gradient(circle at 35% 30%, var(--pn-light), var(--pn-dark));
      color: #FFFFFF;
      border-radius: 50% 45% 55% 48% / 48% 52% 48% 52%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: 900;
      flex-shrink: 0;
      box-shadow: 0 4px 8px rgba(56, 30, 17, 0.25), inset 0 2px 3px rgba(255, 255, 255, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.15);
      transform: rotate(2deg);
    }

    .pn-score--top {
      background: radial-gradient(circle at 35% 30%, var(--pn-gold-start), var(--pn-gold-end));
      box-shadow: 0 4px 10px rgba(194, 120, 3, 0.35), inset 0 2px 4px rgba(255, 255, 255, 0.45);
    }

    .pn-score__label {
      font-size: 7.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      opacity: 0.9;
      margin-top: -3px;
    }

    /* Plus & Minpunten */
    .pn-feedback {
      margin-top: 14px;
      padding-top: 12px;
      border-top: 1px dashed var(--pn-border);
      display: flex;
      flex-direction: column;
      gap: 6px;
      font-size: 12.5px;
    }

    .pn-feedback__item {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 7px 11px;
      border-radius: 10px;
      font-weight: 600;
    }

    .pn-feedback__item--pro {
      background: var(--pn-pro-bg);
      color: var(--pn-pro-text);
      border-left: 3px solid var(--pn-pro-line);
    }

    .pn-feedback__item--con {
      background: var(--pn-con-bg);
      color: var(--pn-con-text);
      border-left: 3px solid var(--pn-con-line);
    }

    .pn-feedback__sign {
      font-weight: 900;
      font-size: 14px;
    }

    /* =========================================
       6. FORMULIER & FOOTER
       ========================================= */
    .pn-form-box {
      margin-top: 28px;
      background: var(--pn-card-bg);
      border: 2px dashed var(--pn-accent);
      border-radius: 22px;
      padding: 22px 20px;
      box-shadow: var(--pn-shadow-warm);
    }

    .pn-form-box__title {
      font-size: 17px;
      font-weight: 800;
      color: var(--pn-dark);
      margin-bottom: 2px;
    }

    .pn-form-box__desc {
      font-size: 12px;
      color: var(--pn-text-muted);
      margin-bottom: 16px;
    }

    .pn-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 12px;
    }

    @media (max-width: 480px) {
      .pn-form-grid {
        grid-template-columns: 1fr;
      }
    }

    .pn-btn {
      width: 100%;
      background: linear-gradient(180deg, var(--pn-medium) 0%, var(--pn-dark) 100%);
      color: #FFFDF9;
      border: none;
      padding: 13px;
      border-radius: 14px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(56, 30, 17, 0.25);
      transition: all 0.15s ease;
    }

    .pn-btn:active {
      transform: scale(0.98);
    }

    .pn-footer {
      text-align: center;
      font-size: 11px;
      font-weight: 600;
      color: var(--pn-text-muted);
      margin-top: 30px;
    }
  </style>


  <!-- Hoofd Container -->
<!-- Hoofd Container -->
<section class="pn-container">

    <!-- Zoekbalk en Sorteer Controls -->
    <div class="pn-controls">
      <input type="text" placeholder="Zoek op merk, winkel of smaak..." class="pn-input">
      <select class="pn-select">
        <option>Hoogste cijfer</option>
        <option>Laagste cijfer</option>
        <option>Laagste prijs</option>
      </select>
    </div>

    <?php
    $query = new WP_Query(array(
      'post_type'      => 'pepernoot',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
    ));
    ?>

    <?php
    $query = new WP_Query(array(
      'post_type'      => 'pepernoot',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
    ));

    $pepernoot_posts = $query->posts;

    usort($pepernoot_posts, function( $a, $b ) {
      $score_a = (float) get_field( 'pepernoot_score', $a->ID );
      $score_b = (float) get_field( 'pepernoot_score', $b->ID );

      if ( $score_a === $score_b ) {
        return strcmp( $b->post_date, $a->post_date );
      }

      return $score_b <=> $score_a;
    });
    ?>

    <?php if ( ! empty( $pepernoot_posts ) ) : ?>

        <div class="pn-list">

            <?php 
            $rank = 0;
        foreach ( $pepernoot_posts as $post ) :
          setup_postdata( $post );
                $rank++;
          $post_id   = get_the_ID();
                    $score     = get_field( 'pepernoot_score', $post_id );
                    $brand     = get_field( 'pepernoot_brand', $post_id );
                    $shop      = get_field( 'pepernoot_shop', $post_id );
                    $price     = get_field( 'pepernoot_price', $post_id );
                    $pro       = get_field( 'pepernoot_pro', $post_id );
                    $con       = get_field( 'pepernoot_con', $post_id );
                    $subtitle  = get_field( 'pepernoot_subtitle', $post_id );
                
                // Nummer 1 krijgt automatisch de gouden 'top' klasse
                $is_top_score = ($rank === 1 || (float)$score >= 8.5);
            ?>

                <div class="pn-card <?php echo $is_top_score ? 'pn-card--top' : ''; ?>">
                    
                    <div class="pn-card__header">
                        <div>
                            <!-- Titel & Link -->
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none;">
                                <span class="pn-card__title"><?php the_title(); ?></span>
                            </a>

                            <!-- Tags: Merk, Winkel, Prijs -->
                            <div class="pn-card__tags">
                                <?php if ( $brand ) : ?>
                                    <span class="pn-tag pn-tag--brand"><?php echo esc_html( $brand ); ?></span>
                                <?php endif; ?>

                                <?php if ( $shop ) : ?>
                                    <span class="pn-tag pn-tag--shop"><?php echo esc_html( $shop ); ?></span>
                                <?php endif; ?>

                                <?php if ( $price ) : ?>
                                    <span class="pn-tag pn-tag--price">€ <?php echo esc_html( str_replace('€', '', $price) ); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Beschrijving -->
                            <?php if ( $subtitle ) : ?>
                                <p class="pn-card__desc"><?php echo esc_html( $subtitle ); ?></p>
                            <?php elseif ( has_excerpt() ) : ?>
                                <p class="pn-card__desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Pepernoot Score Badge -->
                        <?php if ( $score !== '' ) : ?>
                            <div class="pn-score <?php echo $is_top_score ? 'pn-score--top' : ''; ?>">
                                <?php echo esc_html( $score ); ?>
                                <span class="pn-score__label">Cijfer</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Plus- en Minpunten -->
                    <?php if ( $pro || $con ) : ?>
                        <div class="pn-feedback">
                            <?php if ( $pro ) : ?>
                                <div class="pn-feedback__item pn-feedback__item--pro">
                                    <span class="pn-feedback__sign">+</span> <?php echo esc_html( $pro ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( $con ) : ?>
                                <div class="pn-feedback__item pn-feedback__item--con">
                                    <span class="pn-feedback__sign">−</span> <?php echo esc_html( $con ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endforeach; wp_reset_postdata(); ?>

        </div>

    <?php else : ?>
        <p style="text-align:center; color: var(--pn-text-muted);">Er zijn nog geen pepernoten beoordeeld.</p>
    <?php endif; ?>

</section>
<?php get_footer();?>
<?php

/*
Template Name: Overview blocks
Description: Page template that outputs ACF fields (hero + repeater sections)
*/
get_header();?>
  <style>
    :root {
      --bg: #0f172a;
      --card-bg: #1e293b;
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --accent: #6366f1;
      --accent-hover: #4f46e5;
      --border: rgba(255, 255, 255, 0.08);
      --radius: 16px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      background-color: var(--bg);
      color: var(--text);
      line-height: 1.6;
      padding-bottom: 80px;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 20px;
    }

    header {
      padding: 24px 0;
      border-bottom: 1px solid var(--border);
      margin-bottom: 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-weight: 700;
      font-size: 1.3rem;
    }

    /* Knoppen & Algemeen */
    .btn {
      display: inline-block;
      background: var(--accent);
      color: #fff;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: background 0.2s, transform 0.2s;
    }

    .btn:hover {
      background: var(--accent-hover);
      transform: translateY(-2px);
    }

    .section-title {
      font-size: 1.4rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* ==========================================
       1. IMAGE-CONTENT BLOCK (Split layout)
       ========================================== */
    .image-content-block {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center;
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 36px;
      margin-bottom: 50px;
    }

    .image-content-block img {
      width: 100%;
      height: 320px;
      object-fit: cover;
      border-radius: 12px;
    }

    .image-content-text h2 {
      font-size: 1.8rem;
      margin-bottom: 14px;
    }

    .image-content-text p {
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    /* ==========================================
       2. CONTENT BLOCK (Tekst / Informatie kaarten)
       ========================================== */
    .content-block {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 36px;
      margin-bottom: 50px;
    }

    .content-block h2 {
      margin-bottom: 12px;
      font-size: 1.6rem;
    }

    .content-block > p {
      color: var(--text-muted);
      margin-bottom: 28px;
    }

    .content-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
    }

    .info-card {
      background: rgba(15, 23, 42, 0.6);
      border: 1px solid var(--border);
      padding: 20px;
      border-radius: 12px;
      text-decoration: none;
      color: inherit;
      transition: transform 0.2s, border-color 0.2s;
    }

    .info-card:hover {
      transform: translateY(-4px);
      border-color: var(--accent);
    }

    .info-card h3 {
      font-size: 1.1rem;
      margin-bottom: 8px;
      color: var(--text);
    }

    .info-card p {
      font-size: 0.9rem;
      color: var(--text-muted);
    }

    /* ==========================================
       3. STAND-ALONE AFBEELDINGSBLOK
       ========================================== */
    .solo-image-block {
      position: relative;
      border-radius: var(--radius);
      overflow: hidden;
      margin-bottom: 50px;
      border: 1px solid var(--border);
      height: 350px;
    }

    .solo-image-block img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .solo-image-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      padding: 30px;
      background: linear-gradient(transparent, rgba(15, 23, 42, 0.9));
    }

    /* ==========================================
       4. AFBEELDINGEN SLIDER MET LINKS
       ========================================== */
    .slider-section {
      margin-bottom: 50px;
    }

    .slider-container {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      padding-bottom: 16px;
      -webkit-overflow-scrolling: touch;
    }

    /* Custom scrollbar voor slider */
    .slider-container::-webkit-scrollbar {
      height: 8px;
    }
    .slider-container::-webkit-scrollbar-track {
      background: var(--card-bg);
      border-radius: 10px;
    }
    .slider-container::-webkit-scrollbar-thumb {
      background: var(--accent);
      border-radius: 10px;
    }

    .slider-item {
      flex: 0 0 280px;
      scroll-snap-align: start;
      position: relative;
      border-radius: var(--radius);
      overflow: hidden;
      aspect-ratio: 16 / 10;
      border: 1px solid var(--border);
      text-decoration: none;
      display: block;
      transition: transform 0.3s ease, border-color 0.3s ease;
    }

    .slider-item:hover {
      transform: scale(1.03);
      border-color: var(--accent);
    }

    .slider-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .slider-item .badge {
      position: absolute;
      bottom: 12px;
      left: 12px;
      right: 12px;
      background: rgba(15, 23, 42, 0.85);
      backdrop-filter: blur(4px);
      padding: 8px 12px;
      border-radius: 8px;
      color: #fff;
      font-size: 0.9rem;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* ==========================================
       5. PEPERNOOT TOEVOEGEN FORMULIER STYLING
       ========================================== */
    .pepernoot-form-section {
      margin-bottom: 60px;
    }

    .pepernoot-form-card {
      background: #ffffff;
      color: #1f2937;
      border: 1px solid #e5e7eb;
      border-radius: var(--radius);
      padding: 40px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    .pepernoot-form-header {
      margin-bottom: 28px;
      border-bottom: 2px solid #fef3c7;
      padding-bottom: 20px;
    }

    .pepernoot-form-badge {
      display: inline-block;
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
      font-size: 0.82rem;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 999px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 10px;
    }

    .pepernoot-form-title {
      font-size: 1.85rem;
      font-weight: 800;
      color: #92400e;
      margin-bottom: 6px;
    }

    .pepernoot-form-subtitle {
      color: #6b7280;
      font-size: 1rem;
    }

    .form-section-heading {
      font-size: 1.05rem;
      font-weight: 700;
      color: #1f2937;
      background: #fffbeb;
      border-left: 4px solid #d97706;
      padding: 8px 14px;
      border-radius: 0 6px 6px 0;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-collapsible-heading {
      cursor: pointer;
      user-select: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      list-style: none !important;
      transition: background 0.15s ease;
    }

    .form-collapsible-heading::-webkit-details-marker {
      display: none !important;
    }

    .form-collapsible-heading:hover {
      background: #fef3c7;
    }

    .form-section-heading__title {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .form-heading-optional {
      font-size: 0.8rem;
      font-weight: 500;
      color: #92400e;
      opacity: 0.8;
    }

    .form-collapsible-chevron {
      width: 8px;
      height: 8px;
      border-right: 2px solid #92400e;
      border-bottom: 2px solid #92400e;
      transform: rotate(45deg);
      transition: transform 0.25s ease;
      margin-right: 4px;
    }

    details.form-collapsible-section[open] .form-collapsible-chevron {
      transform: rotate(-135deg);
    }

    .form-collapsible-body {
      margin-top: 14px;
    }

    .form-grid {
      display: grid;
      gap: 18px;
      margin-bottom: 8px;
    }

    .form-grid--2 {
      grid-template-columns: repeat(2, 1fr);
    }

    .form-grid--4 {
      grid-template-columns: repeat(4, 1fr);
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 12px;
    }

    .form-group label {
      font-size: 0.9rem;
      font-weight: 700;
      color: #374151;
    }

    .form-group label .required {
      color: #dc2626;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
      width: 100%;
      background: #ffffff;
      color: #111827;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 0.95rem;
      font-family: inherit;
      box-sizing: border-box;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      border-color: #d97706;
      box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
      outline: none;
    }

    .form-submit-wrapper {
      margin-top: 28px;
      text-align: right;
    }

    .btn-submit {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: #ffffff;
      border: none;
      font-size: 1.05rem;
      font-weight: 700;
      padding: 14px 32px;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
      display: inline-flex;
      align-items: center;
      gap: 10px;
      transition: all 0.2s ease;
    }

    .btn-submit:hover {
      background: linear-gradient(135deg, #fbbf24 0%, #b45309 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(217, 119, 6, 0.45);
    }

    .form-alert {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .form-alert--success {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .form-alert--error {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    /* ==========================================
       6. PEPERNOOT REVIEW CARD STYLING
       ========================================== */
    .pepernoot-review-section {
      margin-bottom: 60px;
    }

    .pepernoot-review-card {
      background: #ffffff;
      color: #1f2937;
      border: 1px solid #e5e7eb;
      border-radius: var(--radius);
      padding: 36px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
    }

    .pepernoot-review-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      border-bottom: 2px solid #fef3c7;
      padding-bottom: 20px;
      margin-bottom: 24px;
    }

    .pepernoot-review-header__left {
      flex: 1;
    }

    .pepernoot-review-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 10px;
    }

    .review-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 0.8rem;
      font-weight: 700;
    }

    .review-badge--brand {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fde68a;
    }

    .review-badge--shop {
      background: #e0f2fe;
      color: #0369a1;
      border: 1px solid #bae6fd;
    }

    .review-badge--price {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .pepernoot-review-title {
      font-size: 1.95rem;
      font-weight: 800;
      color: #92400e;
      margin-bottom: 4px;
    }

    .pepernoot-review-subtitle {
      font-size: 1rem;
      color: #6b7280;
    }

    .pepernoot-score-circle {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: #ffffff;
      width: 84px;
      height: 84px;
      border-radius: 50%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
      flex-shrink: 0;
    }

    .pepernoot-score-star {
      font-size: 1rem;
      color: #fef3c7;
    }

    .pepernoot-score-number {
      font-size: 1.55rem;
      font-weight: 800;
      line-height: 1;
    }

    .pepernoot-score-max {
      font-size: 0.7rem;
      opacity: 0.85;
    }

    .pepernoot-review-body {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 28px;
      margin-bottom: 26px;
      align-items: start;
    }

    .pepernoot-review-media {
      border-radius: 12px;
      overflow: hidden;
      height: 220px;
      border: 1px solid #e5e7eb;
    }

    .pepernoot-review-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .pepernoot-review-intro p {
      font-size: 1rem;
      color: #374151;
      line-height: 1.6;
      margin-bottom: 14px;
    }

    .pepernoot-highlights {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .highlight-pill {
      padding: 9px 14px;
      border-radius: 8px;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .highlight-pill--pro {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .highlight-pill--con {
      background: #fef2f2;
      color: #991b1b;
      border: 1px solid #fecaca;
    }

    /* Pros & Cons Accordion */
    .pepernoot-pros-cons-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-bottom: 24px;
    }

    .pros-box,
    .cons-box {
      padding: 18px;
      border-radius: 12px;
      transition: all 0.2s ease;
    }

    .pros-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
    }

    .cons-box {
      background: #fef2f2;
      border: 1px solid #fecaca;
    }

    .collapsible-summary {
      list-style: none !important;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: default;
      user-select: none;
    }

    .collapsible-summary::-webkit-details-marker {
      display: none !important;
    }

    .collapsible-title-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 1.05rem;
      font-weight: 700;
    }

    .pros-box .collapsible-title-wrap {
      color: #166534;
    }

    .cons-box .collapsible-title-wrap {
      color: #991b1b;
    }

    .pros-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      background: #16a34a;
      color: #ffffff;
      border-radius: 50%;
      font-size: 0.75rem;
      font-weight: 800;
    }

    .cons-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      background: #dc2626;
      color: #ffffff;
      border-radius: 50%;
      font-size: 0.75rem;
      font-weight: 800;
    }

    .collapsible-chevron {
      display: none;
      width: 9px;
      height: 9px;
      border-right: 2px solid currentColor;
      border-bottom: 2px solid currentColor;
      transform: rotate(45deg);
      transition: transform 0.25s ease;
      margin-right: 4px;
    }

    details.collapsible-details[open] .collapsible-chevron {
      transform: rotate(-135deg);
    }

    .collapsible-body {
      margin-top: 12px;
    }

    .pros-list,
    .cons-list {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .pros-list li {
      position: relative;
      padding-left: 16px;
      color: #166534;
      font-size: 0.92rem;
    }

    .pros-list li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: #16a34a;
      font-weight: bold;
    }

    .cons-list li {
      position: relative;
      padding-left: 16px;
      color: #991b1b;
      font-size: 0.92rem;
    }

    .cons-list li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: #dc2626;
      font-weight: bold;
    }

    .pepernoot-card-footer {
      text-align: right;
      border-top: 1px solid #f3f4f6;
      padding-top: 18px;
    }

    /* Responsive aanpassingen voor mobiel */
    @media (max-width: 768px) {
      .image-content-block {
        grid-template-columns: 1fr;
      }
      .slider-item {
        flex: 0 0 220px;
      }
      .pepernoot-form-card,
      .pepernoot-review-card {
        padding: 22px;
      }
      .form-grid--2,
      .form-grid--4 {
        grid-template-columns: 1fr;
      }
      .pepernoot-review-header {
        flex-direction: column-reverse;
        align-items: flex-start;
      }
      .pepernoot-score-circle {
        align-self: flex-start;
      }
      .pepernoot-review-body {
        grid-template-columns: 1fr;
      }
      .pepernoot-pros-cons-grid {
        grid-template-columns: 1fr;
        gap: 12px;
      }
      .collapsible-summary {
        cursor: pointer !important;
        padding: 4px 0;
      }
      .collapsible-chevron {
        display: inline-block !important;
      }
      .pepernoot-card-footer {
        text-align: center;
      }
      .pepernoot-card-footer .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <div class="container">

    <div class="image-content-block">
      <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&q=80" alt="Workstation">
      <div class="image-content-text">
        <h2>Uitgelicht Project</h2>
        <p>Dit is een image-content sectie waarin beeld en verhaal direct samenkomen. Ideaal om een belangrijk artikel, portfolio-item of dienst uit te lichten.</p>
        <a href="https://github.com" target="_blank" class="btn">Bekijk Project <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>

    <div class="content-block">
      <h2>Informatie & Bronnen</h2>
      <p>Een overzichtelijk tekstblok met directe verwijzingen naar belangrijke pagina's en categorieën.</p>
      
      <div class="content-grid">
        <a href="https://wikipedia.org" target="_blank" class="info-card">
          <h3>Kennisbank</h3>
          <p>Lees artikelen, documentatie en nuttige handleidingen.</p>
        </a>
        <a href="https://news.ycombinator.com" target="_blank" class="info-card">
          <h3>Tech Nieuws</h3>
          <p>Blijf op de hoogte van de nieuwste trends en updates.</p>
        </a>
        <a href="https://unsplash.com" target="_blank" class="info-card">
          <h3>Media & Assets</h3>
          <p>Vind rechtenvrije foto's en grafische elementen.</p>
        </a>
      </div>
    </div>

    <div class="solo-image-block">
      <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80" alt="Groot landschap">
      <div class="solo-image-overlay">
        <h2>Puur Afbeeldingsblok</h2>
      </div>
    </div>
    
    <div class="solo-image-block">
      <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&q=80" alt="Groot landschap">
    </div>

    <div class="slider-section">
      <h2 class="section-title"><i class="fa-solid fa-sliders"></i> Galerij Slider (Klikbaar)</h2>
      <div class="slider-container">
        
        <a href="https://youtube.com" target="_blank" class="slider-item">
          <img src="https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=500&q=80" alt="YouTube">
          <div class="badge">YouTube <i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="https://spotify.com" target="_blank" class="slider-item">
          <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=500&q=80" alt="Muziek">
          <div class="badge">Spotify <i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="https://reddit.com" target="_blank" class="slider-item">
          <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=500&q=80" alt="Community">
          <div class="badge">Community <i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="https://dribbble.com" target="_blank" class="slider-item">
          <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=500&q=80" alt="Design">
          <div class="badge">Design <i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </a>

        <a href="https://unsplash.com" target="_blank" class="slider-item">
          <img src="https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=500&q=80" alt="Natuur">
          <div class="badge">Fotografie <i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        </a>

      </div>
    </div>

    <!-- ==========================================
       5. PEPERNOOT TOEVOEGEN FORMULIER BLOK
       ========================================== -->
    <?php
    $message = '';
    $message_type = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rick_pepernoot_form_nonce'])) {
        if (wp_verify_nonce($_POST['rick_pepernoot_form_nonce'], 'rick_submit_pepernoot')) {
            $name = sanitize_text_field($_POST['pepernoot_title'] ?? '');
            $subtitle = sanitize_text_field($_POST['pepernoot_subtitle'] ?? '');
            $score = sanitize_text_field($_POST['pepernoot_score'] ?? '');
            $brand = sanitize_text_field($_POST['pepernoot_brand'] ?? '');
            $shop = sanitize_text_field($_POST['pepernoot_shop'] ?? '');
            $price = sanitize_text_field($_POST['pepernoot_price'] ?? '');
            $image = esc_url_raw($_POST['pepernoot_afbeelding'] ?? '');
            $pro = sanitize_text_field($_POST['pepernoot_pro'] ?? '');
            $con = sanitize_text_field($_POST['pepernoot_con'] ?? '');
            $pluspunten = sanitize_textarea_field($_POST['pepernoot_pluspunten'] ?? '');
            $minpunten = sanitize_textarea_field($_POST['pepernoot_minpunten'] ?? '');
            $intro = sanitize_textarea_field($_POST['pepernoot_intro'] ?? '');

            if (!empty($name)) {
                $post_id = wp_insert_post([
                    'post_type' => 'pepernoot',
                    'post_title' => $name,
                    'post_content' => $intro,
                    'post_status' => 'publish',
                ]);

                if ($post_id && !is_wp_error($post_id)) {
                    update_post_meta($post_id, 'pepernoot_subtitle', $subtitle);
                    update_post_meta($post_id, 'pepernoot_score', $score);
                    update_post_meta($post_id, 'pepernoot_brand', $brand);
                    update_post_meta($post_id, 'pepernoot_shop', $shop);
                    update_post_meta($post_id, 'pepernoot_price', $price);
                    update_post_meta($post_id, 'pepernoot_afbeelding', $image);
                    update_post_meta($post_id, 'pepernoot_pro', $pro);
                    update_post_meta($post_id, 'pepernoot_con', $con);
                    update_post_meta($post_id, 'pepernoot_pluspunten', $pluspunten);
                    update_post_meta($post_id, 'pepernoot_minpunten', $minpunten);
                    update_post_meta($post_id, 'pepernoot_intro', $intro);

                    $message = '🎉 Pepernoot "' . esc_html($name) . '" is succesvol toegevoegd!';
                    $message_type = 'success';
                } else {
                    $message = 'Er is een fout opgetreden bij het opslaan van de pepernoot.';
                    $message_type = 'error';
                }
            } else {
                $message = 'Vul minimaal de naam van de pepernoot in.';
                $message_type = 'error';
            }
        }
    }
    ?>

    <div class="pepernoot-form-section">
      <div class="pepernoot-form-card">
        <div class="pepernoot-form-header">
          <div class="pepernoot-form-badge">🍪 Pepernoten Test</div>
          <h2 class="pepernoot-form-title">Nieuwe Pepernoot Toevoegen</h2>
          <p class="pepernoot-form-subtitle">Vul onderstaand formulier in om direct een nieuwe pepernoot review en beoordeling te publiceren.</p>
        </div>

        <?php if (!empty($message)) : ?>
          <div class="form-alert form-alert--<?php echo esc_attr($message_type); ?>">
            <?php echo esc_html($message); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="" class="pepernoot-form-body">
          <?php wp_nonce_field('rick_submit_pepernoot', 'rick_pepernoot_form_nonce'); ?>

          <!-- Sectie 1: Algemeen -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-cookie"></i> 1. Basis Informatie <span class="required">*</span>
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-grid form-grid--2">
                <div class="form-group">
                  <label for="pepernoot_title">Naam / Smaak Pepernoot <span class="required">*</span></label>
                  <input type="text" id="pepernoot_title" name="pepernoot_title" placeholder="bijv. Van Delft Stroopwafel Pepernoten" required>
                </div>
                <div class="form-group">
                  <label for="pepernoot_subtitle">Korte Subtitel</label>
                  <input type="text" id="pepernoot_subtitle" name="pepernoot_subtitle" placeholder="bijv. Knapperige kruidnoten met karamelglazuur">
                </div>
              </div>
            </div>
          </details>

          <!-- Sectie 2: Details & Cijfer -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading" style="margin-top: 18px;">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-star"></i> 2. Beoordeling & Specificaties
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-grid form-grid--4">
                <div class="form-group">
                  <label for="pepernoot_score">Score (1-10) ⭐</label>
                  <input type="number" step="0.1" min="1" max="10" id="pepernoot_score" name="pepernoot_score" placeholder="bijv. 8.5">
                </div>
                <div class="form-group">
                  <label for="pepernoot_brand">Merk</label>
                  <input type="text" id="pepernoot_brand" name="pepernoot_brand" placeholder="bijv. Van Delft">
                </div>
                <div class="form-group">
                  <label for="pepernoot_shop">Winkel / Verkooppunt</label>
                  <input type="text" id="pepernoot_shop" name="pepernoot_shop" placeholder="bijv. Albert Heijn">
                </div>
                <div class="form-group">
                  <label for="pepernoot_price">Prijs (€)</label>
                  <input type="text" id="pepernoot_price" name="pepernoot_price" placeholder="bijv. 2,49">
                </div>
              </div>
            </div>
          </details>

          <!-- Sectie 3: Afbeelding -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading" style="margin-top: 18px;">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-image"></i> 3. Afbeelding
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-group">
                <label for="pepernoot_afbeelding">Afbeelding URL</label>
                <input type="url" id="pepernoot_afbeelding" name="pepernoot_afbeelding" placeholder="https://... of link naar foto">
              </div>
            </div>
          </details>

          <!-- Sectie 4: Highlights -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading" style="margin-top: 18px;">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-thumbs-up"></i> 4. Highlights (Overzicht)
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-grid form-grid--2">
                <div class="form-group">
                  <label for="pepernoot_pro">Belangrijkste Pluspunt (Kort)</label>
                  <input type="text" id="pepernoot_pro" name="pepernoot_pro" placeholder="bijv. Echte stroopwafelsmaak en krokante bite">
                </div>
                <div class="form-group">
                  <label for="pepernoot_con">Belangrijkste Minpunt (Kort)</label>
                  <input type="text" id="pepernoot_con" name="pepernoot_con" placeholder="bijv. Iets aan de zoete kant">
                </div>
              </div>
            </div>
          </details>

          <!-- Sectie 5: Uitgebreide Plus- & Minpunten -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading" style="margin-top: 18px;">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-list-check"></i> 5. Uitgebreide Plus- & Minpunten
                <span class="form-heading-optional">(Optioneel)</span>
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-grid form-grid--2">
                <div class="form-group">
                  <label for="pepernoot_pluspunten">Pluspunten (1 per regel)</label>
                  <textarea id="pepernoot_pluspunten" name="pepernoot_pluspunten" rows="4" placeholder="Krokante structuur&#10;Heerlijke kruidenmix&#10;Hersluitbare zak"></textarea>
                </div>
                <div class="form-group">
                  <label for="pepernoot_minpunten">Minpunten (1 per regel)</label>
                  <textarea id="pepernoot_minpunten" name="pepernoot_minpunten" rows="4" placeholder="Iets prijzig&#10;Snel uitverkocht"></textarea>
                </div>
              </div>
            </div>
          </details>

          <!-- Sectie 6: Beschrijving -->
          <details class="form-collapsible-section" open>
            <summary class="form-section-heading form-collapsible-heading" style="margin-top: 18px;">
              <div class="form-section-heading__title">
                <i class="fa-solid fa-pen"></i> 6. Introductie & Review
              </div>
              <span class="form-collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="form-collapsible-body">
              <div class="form-group">
                <label for="pepernoot_intro">Introductie / Smaakervaring</label>
                <textarea id="pepernoot_intro" name="pepernoot_intro" rows="4" placeholder="Schrijf hier de eerste indruk en review over deze pepernoot..."></textarea>
              </div>
            </div>
          </details>

          <!-- Knop -->
          <div class="form-submit-wrapper">
            <button type="submit" class="btn btn-submit">
              🍪 Pepernoot Opslaan & Publiceren <i class="fa-solid fa-paper-plane"></i>
            </button>
          </div>
        </form>
      </div>
    <!-- ==========================================
       6. PEPERNOOT REVIEW BLOK (MET INKLAPBARE PUNTEN OP MOBIEL)
       ========================================== -->
    <div class="pepernoot-review-section">
      <article class="pepernoot-review-card">
        <div class="pepernoot-review-header">
          <div class="pepernoot-review-header__left">
            <div class="pepernoot-review-badges">
              <span class="review-badge review-badge--brand">🏷️ Van Delft</span>
              <span class="review-badge review-badge--shop">🛒 Albert Heijn</span>
              <span class="review-badge review-badge--price">💶 € 2,49</span>
            </div>
            <h2 class="pepernoot-review-title">Van Delft Stroopwafel Pepernoten</h2>
            <p class="pepernoot-review-subtitle">Knapperige kruidnoten omhuld met echte stroopwafelsmaak en karamel</p>
          </div>
          <div class="pepernoot-score-circle">
            <span class="pepernoot-score-star">★</span>
            <span class="pepernoot-score-number">8.8</span>
            <span class="pepernoot-score-max">/10</span>
          </div>
        </div>

        <div class="pepernoot-review-body">
          <div class="pepernoot-review-media">
            <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&q=80" alt="Stroopwafel pepernoten">
          </div>
          <div class="pepernoot-review-content">
            <div class="pepernoot-review-intro">
              <p>Deze stroopwafel pepernoten van Van Delft zijn een absolute aanrader voor het najaar. De combinatie van kaneel, speculaaskruiden en een zoete laag stroopwafelglazuur smaakt authentiek en heeft een heerlijke bite.</p>
            </div>
            <div class="pepernoot-highlights">
              <div class="highlight-pill highlight-pill--pro">
                <span>👍</span>
                <span><strong>Pluspunt:</strong> Echte stroopwafelsmaak en krokante bite</span>
              </div>
              <div class="highlight-pill highlight-pill--con">
                <span>👎</span>
                <span><strong>Minpunt:</strong> Iets aan de zoete kant</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Inklapbare Plus- en Minpunten op mobiel -->
        <div class="pepernoot-pros-cons-grid">
          <details class="pros-box collapsible-details" open>
            <summary class="collapsible-summary">
              <div class="collapsible-title-wrap">
                <span class="pros-icon">✓</span>
                <span>Pluspunten (3)</span>
              </div>
              <span class="collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="collapsible-body">
              <ul class="pros-list">
                <li>Heerlijke zachte karameltoets</li>
                <li>Knapperige structuur van binnen</li>
                <li>Handige hersluitbare zak</li>
              </ul>
            </div>
          </details>

          <details class="cons-box collapsible-details" open>
            <summary class="collapsible-summary">
              <div class="collapsible-title-wrap">
                <span class="cons-icon">✕</span>
                <span>Minpunten (2)</span>
              </div>
              <span class="collapsible-chevron" aria-hidden="true"></span>
            </summary>
            <div class="collapsible-body">
              <ul class="cons-list">
                <li>Relatief snel uitverkocht in de winkel</li>
                <li>Prijziger dan reguliere kruidnoten</li>
              </ul>
            </div>
          </details>
        </div>

        <div class="pepernoot-card-footer">
          <a href="#" class="btn btn-submit">Bekijk in webshop <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </article>
    </div>

  </div>

</body>
</html>

<?php
get_footer();
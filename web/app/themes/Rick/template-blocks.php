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

    /* Responsive aanpassingen voor mobiel */
    @media (max-width: 768px) {
      .image-content-block {
        grid-template-columns: 1fr;
      }
      .slider-item {
        flex: 0 0 220px;
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

  </div>

</body>
</html>

<?php
get_footer();
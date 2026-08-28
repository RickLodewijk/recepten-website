document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const primaryNav = document.querySelector('[data-primary-nav]');

  if (menuToggle && primaryNav) {
    menuToggle.addEventListener('click', () => {
      const expanded = menuToggle.getAttribute('aria-expanded') === 'true';

      menuToggle.setAttribute('aria-expanded', String(!expanded));
      primaryNav.classList.toggle('is-open', !expanded);
    });
  }

  const searchInput = document.querySelector('.pn-input');
  const sortSelect = document.querySelector('.pn-select');
  const cardList = document.querySelector('.pn-list');

  if (!cardList) return;

  const cards = Array.from(cardList.querySelectorAll('.pn-card'));

  // Helper om data uit een kaart te extraheren
  function extractCardData(card) {
    const title = card.querySelector('.pn-card__title')?.textContent.toLowerCase() || '';
    const brand = card.querySelector('.pn-tag--brand')?.textContent.toLowerCase() || '';
    const shop = card.querySelector('.pn-tag--shop')?.textContent.toLowerCase() || '';
    const desc = card.querySelector('.pn-card__desc')?.textContent.toLowerCase() || '';
    
    // Parse score (bv. '8.8' of '8,8')
    const scoreText = card.querySelector('.pn-score')?.childNodes[0]?.textContent.trim().replace(',', '.') || '0';
    const score = parseFloat(scoreText) || 0;

    // Parse prijs (bv. '€ 4,50' of '4.50')
    const priceText = card.querySelector('.pn-tag--price')?.textContent.replace(/[^0-9,.]/g, '').replace(',', '.') || '9999';
    const price = parseFloat(priceText) || 9999;

    return {
      element: card,
      searchableText: `${title} ${brand} ${shop} ${desc}`,
      score: score,
      price: price
    };
  }

  const cardItems = cards.map(extractCardData);

  // Zoek- en filterfunctie
  function updateList() {
    const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
    const sortMode = sortSelect ? sortSelect.value : 'Hoogste cijfer';

    // 1. Filteren op zoekterm
    const visibleItems = cardItems.filter(item => {
      const isVisible = item.searchableText.includes(query);
      item.element.style.display = isVisible ? '' : 'none';
      return isVisible;
    });

    // 2. Sorteren
    visibleItems.sort((a, b) => {
      if (sortMode.includes('Laagste cijfer')) {
        return a.score - b.score;
      }
      if (sortMode.includes('Laagste prijs') || sortMode.includes('Prijs: laag naar hoog')) {
        return a.price - b.price;
      }
      // Standaard: Hoogste cijfer
      return b.score - a.score;
    });

    // 3. Volgorde bijwerken in de DOM & #1 highlight updaten
    visibleItems.forEach((item, index) => {
      cardList.appendChild(item.element);
      
      // Update top-class highlight voor de huidige nummer 1 in het zoekresultaat
      if (index === 0 && query === '' && sortMode.includes('Hoogste cijfer')) {
        item.element.classList.add('pn-card--top');
        item.element.querySelector('.pn-score')?.classList.add('pn-score--top');
      } else if (index > 0) {
        item.element.classList.remove('pn-card--top');
        item.element.querySelector('.pn-score')?.classList.remove('pn-score--top');
      }
    });
  }

  // Event listeners koppelen
  if (searchInput) {
    searchInput.addEventListener('input', updateList);
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', updateList);
  }

  // Initiële sortering uitvoeren
  updateList();
});
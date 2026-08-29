# Handleiding voor het bouwen van blokken in dit WordPress-theme

Deze handleiding is bedoeld om snel en consistent custom Gutenberg-blokken te bouwen binnen dit project.

## 1. Doel

Gebruik blokken voor herbruikbare contentsecties zoals:
- Hero-secties
- Receptkaarten
- Filters
- CTA-secties
- Intro- of promotie-blokken

Het doel is om stijl en inhoud apart te houden, zodat een editor blokken kan inzetten zonder handmatige HTML te hoeven schrijven.

---

## 2. Projectstructuur

Dit project gebruikt een Bedrock WordPress setup. De theme-folder staat hier:

- `web/app/themes/Rick/`

Belangrijkste mapstructuur:

- `web/app/themes/Rick/functions.php`
- `web/app/themes/Rick/style.css`
- `web/app/themes/Rick/assets/css/`
- `web/app/themes/Rick/template-overview.php`
- `web/app/themes/Rick/inc/`

Voor blokken is het handig om later een structuur te volgen zoals:

```text
web/app/themes/Rick/
  blocks/
    hero/
      block.json
      render.php
      style.css
    recipe-grid/
      block.json
      render.php
      style.css
```

---

## 3. Basisprincipe van Gutenberg-blokken

Een blok bestaat typisch uit:

- `block.json` – registratiedetails van het blok
- `render.php` of `index.php` – PHP-rendering
- `edit.js` – editor UI in Gutenberg
- `save.js` – output die in de editor wordt opgeslagen
- `style.css` – styling voor frontend
- `editor.css` – styling in de editor

Niet elk blok hoeft alles te bevatten. Voor dit project is het vaak genoeg om eerst een server-side rendered blok te maken.

---

## 4. Waar je blokken in WordPress registreert

In WordPress kun je blokken registreren met `register_block_type()`.

Voorbeeld:

```php
function rick_register_blocks() {
    register_block_type(__DIR__ . '/blocks/hero');
    register_block_type(__DIR__ . '/blocks/recipe-grid');
}
add_action('init', 'rick_register_blocks');
```

Plaats dit in:

- `web/app/themes/Rick/functions.php`

---

## 5. Voorbeeld `block.json`

```json
{
  "name": "rick/hero",
  "title": "Rick Hero",
  "category": "widgets",
  "icon": "format-image",
  "description": "Hero sectie met titel, tekst en CTA.",
  "supports": {
    "html": false,
    "align": true,
    "color": {
      "background": true,
      "text": true
    }
  },
  "attributes": {
    "title": {
      "type": "string",
      "default": "Ontdek heerlijke recepten"
    },
    "subtitle": {
      "type": "string",
      "default": "Fresh, simpel en lekker."
    },
    "buttonText": {
      "type": "string",
      "default": "Bekijk recepten"
    },
    "buttonUrl": {
      "type": "string",
      "default": "#"
    }
  },
  "editorScript": "file:./index.js",
  "style": "file:./style.css",
  "render": "file:./render.php"
}
```

---

## 6. Voorbeeld render-bestand

```php
<?php
$title = $attributes['title'] ?? 'Ontdek heerlijke recepten';
$subtitle = $attributes['subtitle'] ?? '';
$buttonText = $attributes['buttonText'] ?? 'Bekijk recepten';
$buttonUrl = $attributes['buttonUrl'] ?? '#';
?>

<section class="rick-hero">
  <div class="container">
    <h2><?php echo esc_html($title); ?></h2>
    <?php if ($subtitle) : ?>
      <p><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>
    <a href="<?php echo esc_url($buttonUrl); ?>" class="button">
      <?php echo esc_html($buttonText); ?>
    </a>
  </div>
</section>
```

---

## 7. Voorbeeld styling

```css
.rick-hero {
  background: #fffbeb;
  padding: 60px 0;
  text-align: center;
}

.rick-hero h2 {
  font-size: 2.5rem;
  color: #92400e;
  margin-bottom: 1rem;
}

.rick-hero .button {
  display: inline-block;
  background: #d97706;
  color: #fff;
  padding: 12px 24px;
  border-radius: 8px;
  text-decoration: none;
}
```

Gebruik dezelfde class-names als in de bestaande homepage-styling, zodat je consistent blijft met het theme.

---

## 8. Gewenste blokken voor dit project

Voor deze site zijn de eerste blokken die logisch zijn:

### 1. Hero blok
- Titel
- Subtekst
- CTA-knop
- Optionele achtergrondkleur

### 2. Recept-grid blok
- Laat recepten zien
- Filter op categorie
- Responsive cards

### 3. Zoek/filter blok
- Zoekveld
- Categorie dropdown
- Reset link

### 4. CTA blok
- Tekst
- Knop
- Afbeelding optioneel

### 5. Inhoudsblok met tekst + afbeelding
- Layout links/rechts
- Responsive op mobiel

---

## 9. Werkwijze voor elk nieuw blok

Gebruik deze workflow:

1. Bepaal doel van het blok
2. Schrijf de contentstructuur uit
3. Kies attributes
4. Maak `block.json`
5. Maak `render.php`
6. Voeg CSS toe
7. Test in Gutenberg-editor
8. Controleer responsive gedrag
9. Controleer of het blok in frontend goed renderen

---

## 10. Stylingregels voor dit theme

Houd je aan deze conventies:

- Gebruik bestaande kleuren uit het theme
- Gebruik dezelfde spacing-structuur
- Laat `.container` en `.button` consistent werken
- Gebruik `max-width: 1100px` voor standaard blokbreedte
- Houd CSS responsive
- Gebruik duidelijke classnamen: `rick-hero`, `rick-card`, `rick-filters`, etc.

Voorbeeld:

```css
.container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px;
}
```

---

## 11. Tips voor een veilige, werkbare ontwikkeling

- Maak eerst een eenvoudig blok, niet teveel tegelijk
- Test per blok in de editor
- Gebruik `esc_html()`, `esc_url()`, `esc_attr()` voor veiligheid
- Vermijd raw HTML in de frontend-rendering
- Houd blokken herbruikbaar in plaats van pagina-specifiek

---

## 12. Snel start-template

Gebruik dit als basis voor een nieuw blok:

```text
web/app/themes/Rick/blocks/
  my-block/
    block.json
    render.php
    style.css
```

### `block.json`

```json
{
  "name": "rick/my-block",
  "title": "My Block",
  "category": "widgets",
  "icon": "smiley",
  "supports": {
    "html": false
  },
  "attributes": {
    "title": {
      "type": "string",
      "default": "Mijn blok"
    }
  },
  "style": "file:./style.css",
  "render": "file:./render.php"
}
```

### `render.php`

```php
<?php
$title = $attributes['title'] ?? 'Mijn blok';
?>

<section class="rick-my-block">
  <h2><?php echo esc_html($title); ?></h2>
</section>
```

### `style.css`

```css
.rick-my-block {
  padding: 40px 0;
  background: #f9fafb;
}
```

---

## 13. Checklist voordat je een blok opneemt

- [ ] Blok heeft een duidelijke functie
- [ ] CSS is responsive
- [ ] Geen hardcoded onduidelijke classnames
- [ ] Block JSON is correct
- [ ] Render file gebruikt veilige escaping
- [ ] Blok werkt in editor en frontend
- [ ] Styling past bij bestaande theme

---

## 14. Volgende stap

Als je blokken wilt gaan bouwen, begin met deze eerste drie:

1. Hero blok
2. Recept-grid blok
3. CTA blok

Zodra deze drie werken, kun je makkelijk uitbreiden met extra blokken.

---

## 15. Extra tip

Gebruik de homepage als referentie. De CSS in [web/app/themes/Rick/assets/css/home.css](web/app/themes/Rick/assets/css/home.css) geeft al een goed voorbeeld van hoe je componenten in dit theme gestyled zijn.

---

Als je wilt, kan ik hierna ook meteen een echt eerste blok voor je maken, bijvoorbeeld:
- `rick/hero`
- `rick/recipe-grid`
- `rick/cta`

en dit direct in het theme plaatsen met de juiste bestanden.

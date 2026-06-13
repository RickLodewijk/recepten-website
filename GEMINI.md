# Project: Rick Recepten

Dit bestand bevat de richtlijnen en conventies voor het bewerken van de Recepten website.

## 📝 Recepten Invoeren (ACF)

Bij het toevoegen of bewerken van recepten (Custom Post Type `recept`) moeten de volgende regels voor de velden worden gevolgd:

### Ingrediënten (Veld: `ingredienten`)
Het veld voor ingrediënten is een tekstgebied waarbij elke regel een specifiek formaat moet hebben voor een correcte weergave in de tabel en de PowerPoint-export.

**Formaat:** `Naam van ingrediënt | Hoeveelheid`

*   **Belangrijk:** Gebruik **geen** HTML-tags zoals `<br />` in dit veld. Gebruik simpelweg een nieuwe regel (Enter) voor elk nieuw ingrediënt.
*   Gebruik de verticale streep (`|`) als scheidingsteken.
*   Zet de **Naam** altijd aan de linkerkant en de **Hoeveelheid** aan de rechterkant.
*   Als er geen hoeveelheid is, laat het dan leeg na de streep (bijv. `Zout | `).

*Voorbeeld (Correct):*
```text
Zelfrijzend bakmeel | 500 g
Harde boter | 250 g
Suiker | 120 g
Plantenspuit met water | (extra nodig)
```

*Voorbeeld (Foutief - NOOIT doen):*
```text
500 g | Zelfrijzend bakmeel (Verkeerde volgorde)
Suiker | 120 g <br /> (Bevat HTML tags)
```

### Bereidingswijze (Veld: `bereidingswijze`)
*   Gebruik platte tekst of HTML.
*   Tussenkoppen kunnen worden aangegeven met `##` (bijv. `## De vulling`). Deze worden in de export automatisch herkend en gestyled.

## 📄 Exporteren & Printen

### PowerPoint Export
De PowerPoint wordt gegenereerd via JavaScript in de browser (`PptxGenJS`).
*   **Locatie:** Recepten -> 📄 Exporteer PPTX.
*   **Layout:** A4Plus (216x303mm).
*   **Auto-paging:** Lange teksten worden automatisch over meerdere slides verdeeld.

### Print als Fotoboek
Op de individuele receptpagina's is een knop "Print als fotoboek" aanwezig.
*   Deze gebruikt een specifiek print-stylesheet in `assets/css/single-recept.css`.
*   **Belangrijk:** Houd de HTML-structuur van de `.recipe-container` intact, omdat de print-stijlen hier direct van afhankelijk zijn voor de zichtbaarheid en page-breaks.

## 🛠️ Ontwikkeling & Docker
De website draait in Docker.
*   **Web-root (in container):** `/var/www/html/web`
*   **WP-Load:** `/var/www/html/web/wp/wp-load.php`
*   Bij het maken van database-wijzigingen via scripts, gebruik altijd `docker-compose exec app php <script>`.

import json
import re

def parse_allerhande(text):
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    if not lines:
        return None
    title = lines[0]
    
    # Find headers
    ing_header = -1
    prep_header = -1
    for i, line in enumerate(lines):
        if "Ingrediënten" in line:
            ing_header = i
        if "Aan de slag" in line:
            prep_header = i

    if ing_header == -1:
        return None

    # Determine ingredients range
    ing_start = ing_header + 1
    if ing_start < len(lines) and "(Op basis van" in lines[ing_start]:
        ing_start += 1
    
    # Ingredients end at prep_header or end of file or "Dit heb je nodig"
    ing_end = len(lines)
    for i in range(ing_start, len(lines)):
        if i == prep_header or "Dit heb je nodig" in lines[i] or "Lekker van Albert Heijn" in lines[i]:
            ing_end = i
            break
            
    raw_ingredients = lines[ing_start:ing_end]
    ingredients = []
    for ing in raw_ingredients:
        # Ignore lines that look like instructions
        if re.match(r'^\d+\.', ing):
            continue
        if len(ing) > 60: # Likely not an ingredient line
            continue
            
        match = re.match(r'^([\d\s\.,/½¼¾]+(?:g|kg|ml|l|el|tl|stuks|pak|zak|blik|fles|mespunt|tenen|stengels|bekertje|bakje|zakje|bosje|plakjes|plak|handjes|handje|kopjes|kopje|stuks|stuks)?)\s+(.*)$', ing, re.IGNORECASE)
        if match:
            amount = match.group(1).strip()
            name = match.group(2).strip()
            ingredients.append(f"{name} | {amount}")
        else:
            ingredients.append(f"{ing} | ")

    # Determine preparation range
    if prep_header != -1:
        prep_start = prep_header + 1
        prep_end = len(lines)
        for i in range(prep_start, len(lines)):
            if i == ing_header or "Lekker van Albert Heijn" in lines[i] or "Algemeen:" in lines[i] or "Variatietip:" in lines[i] or "Bewaartip:" in lines[i] or "Vegantip:" in lines[i]:
                prep_end = i
                break
        
        prep_lines = []
        for i in range(prep_start, prep_end):
            line = re.sub(r'^\d+\.\s+', '', lines[i])
            if line not in [ing.split(' | ')[0] for ing in ingredients]: # Simple dupe check
                prep_lines.append(line)
        preparation = "## Bereiding\n" + "\n".join(prep_lines)
    else:
        preparation = ""
            
    return {
        "title": title,
        "ingredients": "\n".join(ingredients),
        "preparation": preparation
    }

def parse_arla(text):
    # Arla is messy, let's do a basic attempt
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    title = lines[0]
    if "smoothie" not in title.lower() and len(lines) > 1:
        title += " " + lines[1]
    
    # Ingredients and Preparation are often interleaved or in weird order in extract_text
    # Let's just grab the whole text and let the user/AI fix it if needed, 
    # but I'll try to find the keywords.
    
    ing_text = ""
    prep_text = ""
    
    if "Ingrediënten" in text and "Bereidingswijze" in text:
        parts = text.split("Ingrediënten")
        after_ing = parts[1].split("Bereidingswijze")
        ing_raw = after_ing[0]
        prep_raw = after_ing[1]
        
        ing_lines = [l.strip() for l in ing_raw.split('\n') if l.strip()]
        # Skip "Recept voor ..."
        if ing_lines and "Recept voor" in ing_lines[0]:
            ing_lines = ing_lines[1:]
            
        # Arla ingredients often have names first then amounts at the end
        # We'll just put them in as-is for now and manually fix or use a heuristic
        # Actually, let's just join them and let the user see.
        ing_text = "\n".join([f"{l} | " for l in ing_lines])
        
        prep_lines = [l.strip() for l in prep_raw.split('\n') if l.strip()]
        if prep_lines and "Eet smakelijk!" in prep_lines[-1]:
            prep_lines = prep_lines[:-1]
        prep_text = "## Bereiding\n" + "\n".join(prep_lines)
        
    return {
        "title": title,
        "ingredients": ing_text,
        "preparation": prep_text
    }

def main():
    with open("extracted_raw_pdfs.json", "r", encoding="utf-8") as f:
        recipes = json.load(f)
    
    parsed_recipes = []
    for r in recipes:
        if "Allerhande" in r['file'] or "Albert Heijn" in r['file']:
            res = parse_allerhande(r['raw_text'])
            if res:
                res['category'] = 'drinken' if 'smootie' in r['file'] else 'gebak'
                parsed_recipes.append(res)
        elif "Arla" in r['file']:
            res = parse_arla(r['raw_text'])
            if res:
                res['category'] = 'drinken'
                parsed_recipes.append(res)

    # Output PHP script
    php_content = """<?php
require_once(__DIR__ . '/../wp/wp-load.php');

$recipes = [
"""
    for r in parsed_recipes:
        title = r['title'].replace("'", "\\'")
        ingredients = r['ingredients'].replace("'", "\\'")
        preparation = r['preparation'].replace("'", "\\'")
        category = r['category']
        
        php_content += f"    [\n"
        php_content += f"        'title' => '{title}',\n"
        php_content += f"        'ingredients' => '{ingredients}',\n"
        php_content += f"        'preparation' => '{preparation}',\n"
        php_content += f"        'category' => '{category}',\n"
        php_content += f"    ],\n"

    php_content += """];

foreach ($recipes as $recipe_data) {
    $existing = get_page_by_title($recipe_data['title'], OBJECT, 'recept');
    if ($existing) {
        echo "Skipping duplicate: " . $recipe_data['title'] . "\\n";
        continue;
    }

    $post_id = wp_insert_post([
        'post_title'    => $recipe_data['title'],
        'post_type'     => 'recept',
        'post_status'   => 'publish',
    ]);

    if ($post_id) {
        update_field('ingredienten', $recipe_data['ingredients'], $post_id);
        update_field('bereidingswijze', $recipe_data['preparation'], $post_id);
        
        // Set category (taxonomy: recept_category or similar? Let's check)
        // Defaulting to 'categorie' taxonomy based on typical ACF/CPT setups
        wp_set_object_terms($post_id, $recipe_data['category'], 'categorie');
        
        echo "Imported: " . $recipe_data['title'] . "\\n";
    } else {
        echo "Failed to import: " . $recipe_data['title'] . "\\n";
    }
}
"""
    
    with open("web/app/import_pdfs.php", "w", encoding="utf-8") as f:
        f.write(php_content)

if __name__ == "__main__":
    main()

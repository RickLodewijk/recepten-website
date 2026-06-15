import re
import os
import json
from docx import Document

def parse_ingredient_line(line):
    line = line.replace('•', '').strip()
    if not line or len(line) > 100: # Probably not an ingredient if too long
        return None
    
    # Check if it has a separator or looks like an ingredient (amount + name)
    # This regex is a bit loose to catch more cases
    match = re.match(r'^([\d\.,\/\-\s]+(?:g|ml|el|tl|eetlepel|theelepel|stuks|zakje|snuf|cl|dl|kg|cups?|tsp|tbsp|oz)?)\s+(.*)', line, re.IGNORECASE)
    
    if match:
        hoeveelheid = match.group(1).strip()
        naam = match.group(2).strip()
        return f"{naam} | {hoeveelheid}"
    
    # If it's short, consider it an ingredient without amount
    if len(line) < 50:
        return f"{line} | "
    return None

def lees_word_recept(filepath):
    try:
        doc = Document(filepath)
    except Exception as e:
        print(f"Error reading {filepath}: {e}")
        return None
    
    lines = [p.text.strip() for p in doc.paragraphs if p.text.strip()]
    if not lines:
        return None
        
    titel = os.path.splitext(os.path.basename(filepath))[0]
    
    # Heuristic for Title
    if lines:
        titel = lines[0]
        lines = lines[1:]

    ingredienten_lijst = []
    stappen_lijst = []
    
    prep_headers = ["BEREIDING", "BEREIDINGSWIJZE", "HOW TO MAKE", "HOW TO:", "STAPPEN", "DIRECTIONS", "INSTRUCTIONS", "PREPARATION"]
    ingred_headers = ["INGREDIËNTEN", "INGREDIENTEN", "INGREDIENTS", "WAT HEB JE NODIG"]

    mode = "unknown" # can be 'ingredients' or 'preparation'
    
    # First pass: look for explicit headers
    prep_start_index = -1
    ingred_start_index = -1
    
    for i, line in enumerate(lines):
        upper_line = line.upper()
        if any(h in upper_line for h in prep_headers) and len(line) < 40:
            prep_start_index = i
        if any(h in upper_line for h in ingred_headers) and len(line) < 40:
            ingred_start_index = i

    if ingred_start_index != -1 and prep_start_index != -1:
        # We have both
        if ingred_start_index < prep_start_index:
            ingred_lines = lines[ingred_start_index+1 : prep_start_index]
            prep_lines = lines[prep_start_index+1 :]
        else:
            prep_lines = lines[prep_start_index+1 : ingred_start_index]
            ingred_lines = lines[ingred_start_index+1 :]
    elif prep_start_index != -1:
        # Only prep header found
        ingred_lines = lines[:prep_start_index]
        prep_lines = lines[prep_start_index+1 :]
    elif ingred_start_index != -1:
        # Only ingred header found
        ingred_lines = lines[ingred_start_index+1 :]
        prep_lines = []
    else:
        # No headers found. Use simple heuristic.
        # Often ingredients come first as short lines or bullets.
        ingred_lines = []
        prep_lines = []
        found_prep = False
        for line in lines:
            if not found_prep:
                if len(line) > 100 or line.endswith('.') or line.endswith('!'):
                    found_prep = True
                    prep_lines.append(line)
                else:
                    ingred_lines.append(line)
            else:
                prep_lines.append(line)

    for line in ingred_lines:
        parsed = parse_ingredient_line(line)
        if parsed:
            ingredienten_lijst.append(parsed)
        else:
            # If it failed to parse as ingredient but we are in ingred section, 
            # maybe just keep it as is.
            if len(line) < 60:
                ingredienten_lijst.append(f"{line} | ")
    
    for line in prep_lines:
        stappen_lijst.append(line)

    # Clean up title
    titel = titel.strip(':').strip()

    category = "hartig"
    parent_dir = os.path.basename(os.path.dirname(filepath))
    if parent_dir == "zoetig":
        category = "gebak"
    
    return {
        "title": titel,
        "category": category,
        "acf": {
            "meta_info": "Recept voor Zelfgemaakte",
            "ingredienten": "\n".join(ingredienten_lijst),
            "bereidingswijze": "\n".join(stappen_lijst)
        }
    }

files = [
    "recepten/aardappel chicken.docx",
    "recepten/Avondeten/Honing mosterd kip.docx",
    "recepten/Avondeten/Loaded frites met kip.docx",
    "recepten/BBQ chicken wraps.docx",
    "recepten/broodje hete kip.docx",
    "recepten/caprese sandwith.docx",
    "recepten/chili chicken wraps.docx",
    "recepten/egg and cheese bowl.docx",
    "recepten/ei muffins.docx",
    "recepten/fluffy panecakes.docx",
    "recepten/Gevulde aardappels.docx",
    "recepten/grilled chicken.docx",
    "recepten/honing mosterd chicken salade.docx",
    "recepten/kip advocatdo rol.docx",
    "recepten/rozijnen klein brood.docx",
    "recepten/snack/Keto Tortilla Chips met 2 ingrediënten.docx",
    "recepten/tonijn wraps.docx",
    "recepten/zoetig/cookie dough.docx",
    "recepten/nutella harten.docx"
]

results = []
for f in files:
    if os.path.exists(f):
        data = lees_word_recept(f)
        if data:
            results.append(data)

print(json.dumps(results, indent=2))

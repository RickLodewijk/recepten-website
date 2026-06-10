import re
import requests
from docx import Document

# 1. Configuraties (Pas dit aan voor jouw Bedrock site)
WP_URL = "https://jouw-bedrock-site.local/wp-json/wp/v2/recept"
WP_USER = "jouw_gebruikersnaam"
WP_PASSWORD = "xxxx xxxx xxxx xxxx xxxx" # Je Applicatiewachtwoord

def parse_ingredient_line(line):
    """
    Zet een lijn zoals '• 50 g R geraspte kaas' om naar 'R geraspte kaas | 50 g'
    Zodat het perfect matcht met jouw ACF-tabelopzet.
    """
    # Verwijder het opsommingsteken
    line = line.replace('•', '').strip()
    
    # Zoek naar patronen zoals: "100 ml", "50 g", "1 el", "1"
    match = re.match(r'^(\d+(?:\s*(?:g|ml|el|tl|eetlepel|snuf))?)\s+(.*)', line, re.IGNORECASE)
    
    if match:
        hoeveelheid = match.group(1).strip()
        naam = match.group(2).strip()
        return f"{naam} | {hoeveelheid}"
    
    # Als er geen duidelijke eenheid is (bijv. alleen "snuf peper en zout")
    return f"{line} | "

def lees_bestaand_word_recept(bestandsnaam):
    doc = Document(bestandsnaam)
    
    titel = ""
    ingredienten_lijst = []
    stappen_lijst = []
    
    huidige_sectie = None
    
    for index, para in enumerate(doc.paragraphs):
        text = para.text.strip()
        if not text:
            continue
            
        # De allereerste gevulde regel in het document is altijd de titel
        if index == 0 or (titel == "" and text):
            titel = text
            continue
            
        # Secties detecteren op basis van jouw koppen
        if text.upper() == "INGREDIËNTEN":
            huidige_sectie = "ingredienten"
            continue
        elif text.upper() == "BEREIDING":
            huidige_sectie = "bereiding"
            continue
        elif text in ["Brood en gebak", "Anders"]: # Sla deze subkoppen over
            continue
            
        # Data verwerken op basis van de actieve sectie
        if huidige_sectie == "ingredienten" and text.startswith('•'):
            geformatteerd_ingredient = parse_ingredient_line(text)
            ingredienten_lijst.append(geformatteerd_ingredient)
            
        elif huidige_sectie == "bereiding":
            stappen_lijst.append(text)

    return {
        "title": titel,
        "acf": {
            "meta_info": "Recept voor Zelfgemaakte", # Standaard waarde
            "bereidingstijd": "30 minuten", # Kun je eventueel later handmatig of dynamisch doen
            "intro_tekst": f"Heerlijke zelfgemaakte {titel.lower()}.",
            "ingredienten": "\n".join(ingredienten_lijst),
            "bereidingswijze": "\n".join(stappen_lijst),
            "bakker_tip": ""
        }
    }

def verzend_naar_wordpress(payload):
    # Voeg publicatiestatus toe
    payload["status"] = "publish"
    
    response = requests.post(WP_URL, json=payload, auth=(WP_USER, WP_PASSWORD))
    
    if response.status_code == 201:
        print(f"🎉 Succes! Recept '{payload['title']}' staat live.")
    else:
        print(f"❌ Fout bij uploaden: {response.status_code}")
        print(response.json())

# Uitvoeren zonder aanpassingen aan je Word-bestand!
recept_data = lees_bestaand_word_recept("ei muffins.docx")
verzend_naar_wordpress(recept_data)
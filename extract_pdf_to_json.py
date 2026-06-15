import os
import json
import glob
from pdfminer.high_level import extract_text

def clean_text(text):
    if not text:
        return ""
    text = text.replace("IngrediÙnten", "Ingrediënten")
    return text.strip()

pdf_files = glob.glob("recepten/**/*.pdf", recursive=True)
all_data = {}

for f in pdf_files:
    print(f"Processing {f}...")
    try:
        text = extract_text(f)
        all_data[f] = clean_text(text)
    except Exception as e:
        print(f"Error reading {f}: {e}")

with open("pdf_content.json", "w", encoding="utf-8") as f:
    json.dump(all_data, f, ensure_ascii=False, indent=2)

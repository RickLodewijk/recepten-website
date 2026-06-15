import os
import json
from pdfminer.high_level import extract_text

def process_pdfs(directories):
    all_recipes = []
    for directory in directories:
        if not os.path.exists(directory):
            print(f"Directory {directory} does not exist.")
            continue
            
        for filename in os.listdir(directory):
            if filename.endswith(".pdf"):
                file_path = os.path.join(directory, filename)
                print(f"Processing {file_path}...")
                try:
                    text = extract_text(file_path)
                    all_recipes.append({
                        "file": file_path,
                        "raw_text": text
                    })
                except Exception as e:
                    print(f"Error processing {file_path}: {e}")
    
    with open("extracted_raw_pdfs.json", "w", encoding="utf-8") as f:
        json.dump(all_recipes, f, ensure_ascii=False, indent=2)

if __name__ == "__main__":
    process_pdfs(["recepten/smootie", "recepten/zoetig"])

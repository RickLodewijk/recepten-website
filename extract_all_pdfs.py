import os
import json
import glob
from pdfminer.high_level import extract_text

pdf_files = glob.glob("recepten/**/*.pdf", recursive=True)

for f in pdf_files:
    print(f"--- FILE: {f} ---")
    try:
        text = extract_text(f)
        print(text)
    except Exception as e:
        print(f"Error reading {f}: {e}")
    print("\n")

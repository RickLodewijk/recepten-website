from docx import Document
import sys

def debug_docx(filepath):
    print(f"--- Debugging: {filepath} ---")
    try:
        doc = Document(filepath)
        for i, para in enumerate(doc.paragraphs):
            print(f"{i}: [{para.text}]")
    except Exception as e:
        print(f"Error: {e}")

debug_docx("recepten/BBQ chicken wraps.docx")
debug_docx("recepten/aardappel chicken.docx")

#!/usr/bin/env python3
import os
import re

icons_dir = "assets/icons"

for filename in os.listdir(icons_dir):
    if filename.endswith(".svg"):
        filepath = os.path.join(icons_dir, filename)
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()
        
        # Replace dark colors with white
        content = content.replace('#374151', '#ffffff')
        content = content.replace('fill="none"', 'fill="none"')  # Keep fill none as is
        
        with open(filepath, "w", encoding="utf-8") as f:
            f.write(content)
        
        print(f"Updated: {filename}")

print("All icons converted to white!")

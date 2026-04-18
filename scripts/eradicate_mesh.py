import os
import re

search_path = r'c:\wamp64\www\uoc-sports'
# Pattern for the entire CSS block
mesh_css_pattern = re.compile(r'\.mesh-sporty\s*\{[^}]*\}', re.MULTILINE | re.DOTALL)
# Pattern for the class name in HTML
mesh_class_pattern = re.compile(r'\s*mesh-sporty\s*', re.IGNORECASE)

print(f"Starting eradication of mesh-sporty from {search_path}...")

files_cleaned = 0
for root, dirs, files in os.walk(search_path):
    if '.git' in dirs:
        dirs.remove('.git')
    for file in files:
        if file.endswith(('.php', '.css')):
            path = os.path.join(root, file)
            try:
                with open(path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                
                # 1. Remove the CSS definition
                temp_content = mesh_css_pattern.sub('', content)
                
                # 2. Remove the class from HTML elements (e.g., <body class="mesh-sporty">)
                # We target matches inside class="..."
                def clean_class_attr(match):
                    full_attr = match.group(0)
                    cleaned = mesh_class_pattern.sub(' ', full_attr)
                    # Clean up multiple spaces and empty class attributes
                    cleaned = re.sub(r'\s+', ' ', cleaned)
                    cleaned = cleaned.replace('class=" "', 'class=""')
                    # If it's just class="", and it's the only attribute on body, we could keep it or clean it
                    return cleaned

                # Regex to find class="...mesh-sporty..."
                new_content = re.sub(r'class="[^"]*mesh-sporty[^"]*"', clean_class_attr, temp_content)
                
                if content != new_content:
                    print(f"Cleaning: {path}")
                    with open(path, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    files_cleaned += 1
            except Exception as e:
                print(f"Error processing {path}: {e}")

print(f"Done! Cleaned {files_cleaned} files.")

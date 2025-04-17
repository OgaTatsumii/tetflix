import os

def tree(dir_path, indent='', file=None):
    for item in os.listdir(dir_path):  # Bỏ sorted() để giữ nguyên thứ tự
        full_path = os.path.join(dir_path, item)
        if os.path.isdir(full_path):
            file.write(f"{indent}├── {item}/\n")
            tree(full_path, indent + "│   ", file)
        else:
            file.write(f"{indent}├── {item}\n")

with open("structure.txt", "w", encoding="utf-8") as f:
    tree(".", file=f)

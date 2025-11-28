import json
import subprocess
from textx import metamodel_from_file
from textx.export import model_export
import sys  # for stderr

def model_to_dict(obj, visited=None):
    if visited is None:
        visited = set()

    if id(obj) in visited:
        return None

    if isinstance(obj, list):
        return [model_to_dict(o, visited) for o in obj]

    if hasattr(obj, '__class__') and hasattr(obj, '__dict__'):
        visited.add(id(obj))
        d = {}
        for k, v in obj.__dict__.items():
            if k.startswith('_'):
                continue
            d[k] = model_to_dict(v, visited)
        visited.remove(id(obj))
        return d

    return obj

# Load grammar
mm = metamodel_from_file('kure_rules.tx')

# Parse rules
model = mm.model_from_file('rules.txt')

# Export AST as JSON
ast_dict = model_to_dict(model)
with open('ast.json', 'w', encoding='utf-8') as f:
    json.dump(ast_dict, f, ensure_ascii=False, indent=4)

# Export DOT and PNG
dot_file = 'ast.dot'
png_file = 'ast.png'
model_export(model, dot_file)

dot_executable = r"C:\Program Files\Graphviz\bin\dot.exe"

try:
    subprocess.run([dot_executable, "-Tpng", dot_file, "-o", png_file], check=True)
    print(f"PNG generated: {png_file}", file=sys.stderr)  # log to stderr
except FileNotFoundError:
    print(f"Graphviz not found at {dot_executable}", file=sys.stderr)

# ONLY JSON to stdout 
print(json.dumps(ast_dict, ensure_ascii=False))
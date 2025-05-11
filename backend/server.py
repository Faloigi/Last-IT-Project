from flask import Flask, request, jsonify
from script import generate_response
from flask_cors import CORS

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "http://localhost:5173"}})

@app.route('/', methods=['POST'])
def get_prompt():
    data = request.json
    prompt = data.get('prompt', '')
    print(prompt)
    # Logica di generazione (qui un esempio fittizio)
    response = generate_response(prompt)
    
    
    return jsonify({'response': response})

if __name__ == '__main__':
    app.run(port=5000)


from flask import Flask

app = Flask(__name__)
app.secret_key = 'your_secret_key'  # Cambia 'your_secret_key' a una clave secreta única

from app import routes

from flask import render_template, request, redirect, url_for, flash, jsonify
from app import app
import os
import json

# Simulación de almacenamiento de usuarios
users = {'jp': '123'}

# Ruta del archivo de logs
log_file_path = os.path.join(app.root_path, 'static', 'log.json')

# Simulación de lectura de logs desde un archivo JSON
def read_logs():
    try:
        with open(log_file_path, 'r') as f:
            logs = json.load(f)
    except FileNotFoundError:
        logs = []
    except Exception as e:
        flash(f'Error reading log file: {str(e)}', 'danger')
        logs = []
    return logs

# Simulación de escritura de logs en un archivo JSON
def write_logs(logs):
    try:
        with open(log_file_path, 'w') as f:
            json.dump(logs, f)
        return True
    except Exception as e:
        return False, str(e)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/add_user', methods=['GET', 'POST'])
def add_user():
    if request.method == 'POST':
        username = request.form['usuario_name']
        password = request.form['psw_name']
        confirm_password = request.form['psw_name2']

        if password != confirm_password:
            flash('Las contraseñas no coinciden', 'danger')
        elif username in users:
            flash('El usuario ya existe', 'danger')
        else:
            users[username] = password
            flash('Usuario creado exitosamente', 'success')
            return redirect(url_for('index'))
    
    return render_template('add_user.html')

@app.route('/login', methods=['POST'])
def login():
    username = request.form['usuario_name']
    password = request.form['psw_name']
    
    if username in users and users[username] == password:
        flash('Inicio de sesión exitoso', 'success')
        return redirect(url_for('dashboard'))
    else:
        flash('Nombre de usuario o contraseña incorrectos', 'danger')
        return redirect(url_for('index'))

@app.route('/dashboard')
def dashboard():
    logs = read_logs()
    if logs:
        last_log = logs[-1]
    else:
        last_log = {'hum': 0, 'mov': 0, 'dist': 0, 'status': '0', 'time': '00:00:00'}
    
    translated_status = translate_status(last_log['status'])
    
    return render_template('dashboard.html', hum=last_log['hum'], mov=last_log['mov'], dist=last_log['dist'], status=last_log['status'], time=last_log['time'], translated_status=translated_status, message='Mostrando datos actuales')

@app.route('/update_hum', methods=['POST'])
def update_hum():
    hum = request.json.get('hum')
    if hum is None:
        return jsonify({'error': 'Missing data'}), 400

    logs = read_logs()
    if logs:
        logs[-1]['hum'] = hum
    else:
        logs.append({'hum': hum, 'mov': 0, 'dist': 0, 'status': 0, 'time': '00:00:00'})

    success, message = write_logs(logs)
    if not success:
        return jsonify({'error': f'Error writing to log file: {message}'}), 500

    return jsonify({'success': 'Humidity updated successfully'}), 200

@app.route('/update_mov', methods=['POST'])
def update_mov():
    mov = request.json.get('mov')
    if mov is None:
        return jsonify({'error': 'Missing data'}), 400

    logs = read_logs()
    if logs:
        logs[-1]['mov'] = mov
    else:
        logs.append({'hum': 0, 'mov': mov, 'dist': 0, 'status': 0, 'time': '00:00:00'})

    success, message = write_logs(logs)
    if not success:
        return jsonify({'error': f'Error writing to log file: {message}'}), 500

    return jsonify({'success': 'Movement updated successfully'}), 200

@app.route('/update_dist', methods=['POST'])
def update_dist():
    dist = request.json.get('dist')
    if dist is None:
        return jsonify({'error': 'Missing data'}), 400

    logs = read_logs()
    if logs:
        logs[-1]['dist'] = dist
    else:
        logs.append({'hum': 0, 'mov': 0, 'dist': dist, 'status': 0, 'time': '00:00:00'})

    success, message = write_logs(logs)
    if not success:
        return jsonify({'error': f'Error writing to log file: {message}'}), 500

    return jsonify({'success': 'Distance updated successfully'}), 200

@app.route('/update_status_time', methods=['POST'])
def update_status_time():
    status = request.json.get('status')
    time = request.json.get('time')
    if None in [status, time]:
        return jsonify({'error': 'Missing data'}), 400

    logs = read_logs()
    if logs:
        logs[-1]['status'] = status
        logs[-1]['time'] = time
    else:
        logs.append({'hum': 0, 'mov': 0, 'dist': 0, 'status': status, 'time': time})

    success, message = write_logs(logs)
    if not success:
        return jsonify({'error': f'Error writing to log file: {message}'}), 500

    return jsonify({'success': 'Status and time updated successfully'}), 200

@app.route('/get_logs', methods=['GET'])
def get_logs():
    logs = read_logs()
    return jsonify(logs)

def translate_status(status):
    if status == '0':
        return 'activo'
    elif status == '1':
        return 'detenido'
    elif status == '2':
        return 'terminado'
    else:
        return 'desconocido'

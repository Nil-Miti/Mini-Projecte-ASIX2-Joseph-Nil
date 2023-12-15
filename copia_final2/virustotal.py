import os
import hashlib
import requests
import json
import shutil
from pymongo import MongoClient

# Definir la ubicación del directorio de carga, la carpeta para archivos infectados y no infectados.
directorios = {
    'carga': '/var/www/servidor/uploads/',
    'infectados': '/var/www/servidor/infectados/',
    'limpios': '/var/www/servidor/limpios/',
}

# Conectar a MongoDB (asegúrate de tener MongoDB instalado y en ejecución)
client = MongoClient('localhost', 27017)
db = client['registros']
archivos_collection = db['archivos']

# Verificamos si el directorio de carga está vacío.
if not os.listdir(directorios['carga']):
    print("La carpeta de carga está vacía. No hay archivos para procesar.")
    exit()

# Función para calcular el hash de un archivo.
def calcular_hash(archivo):
    sha256 = hashlib.sha256()
    with open(archivo, "rb") as f:
        while True:
            datos = f.read(65536)  # Leer en bloques de 64 KB.
            if not datos:
                break
            sha256.update(datos)
    return sha256.hexdigest()

# Función para obtener el informe de VirusTotal.
def get_file_report(url):
    api_key = '7d3b0c36cb6ffa9836bbe4069bd6f7f1c16e1b52f7f64fb2365eb8fdbd781343'
    headers = {
        'x-apikey': api_key,
    }

    response = requests.get(url, headers=headers)
    return response.json()

# Función para obtener el número de análisis maliciosos.
def obtener_numero_maliciosos(informe):
    return informe.get('data', {}).get('attributes', {}).get('last_analysis_stats', {}).get('malicious', 0)

# Procesar cada archivo en el directorio de carga.
for archivo in os.listdir(directorios['carga']):
    ruta_completa = os.path.join(directorios['carga'], archivo)
    
    # Calcular el hash del archivo.
    archivo_hash = calcular_hash(ruta_completa)

    # Enviar a VirusTotal y obtener el informe.
    api_url = 'https://www.virustotal.com/api/v3/files'
    headers = {'x-apikey': '7d3b0c36cb6ffa9836bbe4069bd6f7f1c16e1b52f7f64fb2365eb8fdbd781343'}
    with open(ruta_completa, 'rb') as file:
        response = requests.post(api_url, headers=headers, files={'file': file})

        # Obtener la URL del informe.
        result = response.json()
        url_self = result['data']['links']['self']
        informe = get_file_report(url_self)

        # Obtener el número de informes maliciosos.
        num_maliciosos = obtener_numero_maliciosos(informe)

        # Determinar el estado del archivo y moverlo.
        if num_maliciosos > 1:
            estado = 'Infectado'
            destino = directorios['infectados']
            print("El archivo subido tiene contenido malicioso")
        else:
            estado = 'Limpio'
            destino = directorios['limpios']
            print("El archivo subido se ha subido correctamente")

        # Mover el archivo a la carpeta adecuada.
        shutil.move(ruta_completa, os.path.join(destino, archivo))

        # Insertar información del archivo en la colección correspondiente en MongoDB.
        archivos_collection.insert_one({
            'nombre': archivo,
            'ubicacion': ruta_completa,
            'hash': archivo_hash,
            'estado': estado,
            'alert_severity': informe.get('data', {}).get('attributes', {}).get('last_analysis_stats', {}).get('malicious', 0),
            # Añade más campos según sea necesario
        })

# Cerrar la conexión a MongoDB.
client.close()

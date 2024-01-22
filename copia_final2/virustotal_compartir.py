from pymongo import MongoClient
import os
import hashlib
import requests
import shutil
import sys

titulo = sys.argv[1]

directorios = {
    'infectados': '/var/www/servidor/infectados/',
    'limpios': f'/var/www/servidor/archivos_compartidos/{titulo}/'
}
client = MongoClient('localhost', 27017)
db = client['registros']
archivos_collection = db['archivos']

# Verificamos si el directorio de carga está vacío.
# Si no hay archivos para procesar, se muestra un mensaje y el programa se cierra.
if not os.listdir(directorios['limpios']):
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
# Función para enviar archivo a VirusTotal y obtener datos adicionales.
def enviar_a_virustotal(archivo):
    api_key = '7d3b0c36cb6ffa9836bbe4069bd6f7f1c16e1b52f7f64fb2365eb8fdbd781343'
    url = 'https://www.virustotal.com/api/v3/files'
    headers = {
        'x-apikey': api_key,
    }

    with open(archivo, 'rb') as file:
        response = requests.post(url, headers=headers, files={'file': file})
        result = response.json()
        url_self = result['data']['links']['self']  # Guardar el valor de 'self'
    return url_self

# Función para obtener el informe de VirusTotal de una URL 'self'.
def get_file_report(url_self):
    headers = {
        "accept": "application/json",
        "x-apikey": "7d3b0c36cb6ffa9836bbe4069bd6f7f1c16e1b52f7f64fb2365eb8fdbd781343"
    }

    response = requests.get(url_self, headers=headers)
    informe = response.json()
    return informe

def obtener_numero_maliciosos(informe):
    stats = informe.get('data', {}).get('attributes', {}).get('stats', {})
    return stats.get('malicious', 0)

# Recorrer los archivos en el directorio de entrada
for root, dirs, files in os.walk(directorios['limpios']):
    for file in files:
        archivo = os.path.join(root, file)

        # Calcular el hash del archivo.
        archivo_hash = calcular_hash(archivo)

        # Enviar el archivo a VirusTotal y obtener la URL 'self'
        url_self = enviar_a_virustotal(archivo)

        # Obtener el informe de archivo de VirusTotal utilizando la URL 'self'
        informe = get_file_report(url_self)

        # Obtener el número de informes maliciosos
        num_maliciosos = obtener_numero_maliciosos(informe)

        # Determinar si el archivo está infectado o limpio y moverlo
        if num_maliciosos > 1:
            estado = 'Infectado'
            destino = directorios['infectados']
            print("El archivo subido está infectado")
        else:
            estado = 'Limpio'
            destino = directorios['limpios']
            print("El archivo se ha subido correctamente")

        # Mover el archivo a la carpeta adecuada.
        shutil.move(archivo, os.path.join(destino, file))

        # Insertar información del archivo en la base de datos MongoDB
        archivos_collection.insert_one({
            'nombre': file,
            'ubicacion': archivo,  # Puedes cambiar esto según lo que desees almacenar
            'hash': archivo_hash,
            'estado': estado,
            'alert_severity': informe.get('data', {}).get('attributes', {}).get('last_analysis_stats', {}).get('malicious', 0),
            # Añade más campos según sea necesario
        })

# Cerrar la conexión a MongoDB.
client.close()

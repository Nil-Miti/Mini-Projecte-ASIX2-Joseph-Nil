<?php
// Verifica si el parámetro 'archivo' está presente en la URL
if (isset($_GET['archivo'])) {
    $nombreArchivo = $_GET['archivo'];
    $nombre_grupo = $_GET['grupo'];    
    // Construye la ruta completa del archivo
    $rutaArchivo = "/var/www/servidor/grupos/$nombre_grupo/$nombreArchivo";

    // Verifica si el archivo existe
    if (file_exists($rutaArchivo)) {
        // Configura las cabeceras para la descarga del archivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($rutaArchivo));
        ob_clean();
        flush();
        readfile($rutaArchivo);
        exit;
    } else {
        // El archivo no existe, muestra un mensaje de error
        echo "El archivo no existe.";
    }
} else {
    // No se proporcionó el nombre del archivo, muestra un mensaje de error
    echo "No se proporcionó el nombre del archivo.";
}
?>

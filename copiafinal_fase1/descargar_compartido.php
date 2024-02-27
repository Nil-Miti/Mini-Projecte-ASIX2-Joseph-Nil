<?php
// Verificar si se ha proporcionado un archivo para descargar
if(isset($_GET['file'])) {
    $ruta_archivo = "/var/www/servidor/archivos_compartidos/" . $_GET['file']; // Ajusta la ruta según tu estructura
    // Verificar que el archivo exista y sea seguro
    if(file_exists($ruta_archivo) && is_file($ruta_archivo)) {
        // Establecer encabezados para la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($ruta_archivo) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta_archivo));
        // Leer el archivo y enviarlo al cliente
        readfile($ruta_archivo);
        exit;
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "No se ha especificado un archivo para descargar.";
}
?>

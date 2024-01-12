<?php
session_start();

if ((isset($_SESSION['id_usuario'])) && ($_SESSION['id_usuario'] == 1)) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Archivo</title>
</head>
<body>
    <h1>Subir Archivo</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Seleccionar Archivo:</label>
        <input type="file" name="file" id="file" required>
        <br>
        <input type="submit" value="Subir Archivo">
    </form>
<?php
} else {
    echo "<h1>No te has validado! Clic en 'Volver al inicio' y inicia sesión.</h1>";
}
?>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0) {
        $target_dir = "uploads/";  // Carpeta donde se guardarán los archivos
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $uploadOk = 1;

        // ... (código de verificación existente)

        // Verificar si $uploadOk es 0 por un error
        if ($uploadOk == 0) {
            echo "Lo siento, tu archivo no fue cargado.";
        } else {
            // Si todo está bien, intenta cargar el archivo
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                echo "El archivo " . basename($_FILES["file"]["name"]) . " ha sido cargado.";

                // Llamar al script de Python para el análisis
                $python_script = "/var/www/servidor/virustotal.py";  // Reemplaza con la ruta correcta
                $output = shell_exec("python3 $python_script");

                // Mostrar el resultado del análisis
                echo "<br>Resultado del análisis: $output";
            } else {
                echo "Lo siento, hubo un error al cargar tu archivo.";
            }
        }
    } else {
        echo "Por favor, selecciona un archivo antes de intentar subirlo.";
    }
}
?>

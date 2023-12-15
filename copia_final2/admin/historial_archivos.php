<?php
try {
    // Conectar a MongoDB (asegúrate de tener MongoDB instalado y en ejecución)
    $cliente = new MongoDB\Client('mongodb://localhost:27017');
    $baseDeDatos = $cliente->registros;
    $coleccionArchivos = $baseDeDatos->archivos;

    // Obtener todos los documentos de la colección
    $archivos = $coleccionArchivos->find();
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Error de conexión a MongoDB: " . $e->getMessage();
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archivos Subidos</title>
</head>
<body>
    <h1>Archivos Subidos</h1>

    <?php if ($archivos->count() > 0): ?>
        <table border="1">
            <tr>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Hash</th>
                <th>Estado</th>
                <th>Alert Severity</th>
                <!-- Agrega más columnas según sea necesario -->
            </tr>

            <?php foreach ($archivos as $archivo): ?>
                <tr>
                    <td><?php echo $archivo['nombre']; ?></td>
                    <td><?php echo $archivo['ubicacion']; ?></td>
                    <td><?php echo $archivo['hash']; ?></td>
                    <td><?php echo $archivo['estado']; ?></td>
                    <td><?php echo $archivo['alert_severity']; ?></td>
                    <!-- Agrega más columnas según sea necesario -->
                </tr>
            <?php endforeach; ?>

        </table>
    <?php else: ?>
        <p>No hay archivos subidos.</p>
    <?php endif; ?>

</body>
</html>

<?php
require_once 'includes/functions.php'; // Asegúrate de incluir las funciones si se necesita acceso a la BD o validación

// Procesa el formulario si se envía con método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    if (validarHabitacionDisponible($_POST['habitacion'], $_POST['fecha_ingreso'], $_POST['fecha_salida'])) {
        if (agregarCliente($_POST)) {
            header("Location: index.php?message=Cliente registrado exitosamente");
            exit;
        } else {
            $response['message'] = 'Error al registrar el cliente.';
        }
    } else {
        $response['message'] = 'La habitación no está disponible para las fechas seleccionadas.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Cliente - Sistema de Gestión Hotelera</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('img/fondo.avif'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    <div class="container">
        <h2>Registrar Nuevo Cliente</h2>
        
        <?php if (isset($response['message'])) : ?>
            <p class="error-message"><?php echo $response['message']; ?></p>
        <?php endif; ?>

        <form action="nuevo_cliente.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="documento">Documento:</label>
            <input type="text" id="documento" name="documento" required>

            <label for="habitacion">Número de Habitación:</label>
            <input type="number" id="habitacion" name="habitacion" required>

            <label for="tipo_habitacion">Tipo de Habitación:</label>
            <select id="tipo_habitacion" name="tipo_habitacion" required>
                <option value="simple">Simple</option>
                <option value="doble">Doble</option>
                <option value="suite">Suite</option>
            </select>

            <label for="fecha_ingreso">Fecha de Ingreso:</label>
            <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>

            <label for="fecha_salida">Fecha de Salida:</label>
            <input type="date" id="fecha_salida" name="fecha_salida" required>

            <label for="observaciones">Observaciones:</label>
            <textarea id="observaciones" name="observaciones"></textarea>

            <button type="submit" class="button">Registrar Cliente</button>
        </form>

        <a href="index.php" class="button">Volver al Inicio</a>
    </div>
</body>
</html>

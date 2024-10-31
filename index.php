<?php
require_once 'includes/functions.php';

// Obtener estadísticas
$estadisticas = obtenerEstadisticas();
$habitacionesOcupadas = obtenerHabitacionesOcupadas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Hotelera</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body style="background-image: url('img/fondo.avif'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    <div class="container">
        <h1>Sistema de Gestión Hotelera</h1>
        
        <!-- Botón para agregar nuevo cliente -->
        <div class="add-client-button">
            <a href="nuevo_cliente.php" class="button">Nuevo Cliente</a>
        </div>

        <!-- Sección de estadísticas -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Habitaciones Ocupadas</h3>
                <div class="stat-number"><?php echo $estadisticas['habitaciones_ocupadas']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Clientes</h3>
                <div class="stat-number"><?php echo $estadisticas['total_clientes']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Próximos Check-outs</h3>
                <div class="stat-number"><?php echo $estadisticas['proximos_checkouts']; ?></div>
            </div>
        </div>

        <!-- Lista de habitaciones ocupadas y su tipo -->
        <div class="occupied-rooms-container">
            <h2>Habitaciones Ocupadas</h2>
            <?php if (!empty($habitacionesOcupadas)) : ?>
                <ul>
                    <?php foreach ($habitacionesOcupadas as $habitacion) : ?>
                        <li>Habitación <?php echo $habitacion['numero']; ?> - Tipo: <?php echo ucfirst($habitacion['tipo']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No hay habitaciones ocupadas actualmente.</p>
            <?php endif; ?>
        </div>

        <!-- Resto del contenido (búsqueda, filtros, tabla de clientes, etc.) -->
        <!-- ... -->
    </div>
</body>
</html>

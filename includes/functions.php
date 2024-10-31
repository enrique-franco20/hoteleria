<?php
require_once 'config/config.php'; // Configuración de MongoDB

function agregarCliente($data) {
    global $clientesCollection;
    
    try {
        $estado = calcularEstado($data['fecha_ingreso'], $data['fecha_salida']);
        
        $documento = [
            'nombre' => $data['nombre'],
            'documento' => $data['documento'],
            'habitacion' => intval($data['habitacion']),
            'tipo_habitacion' => $data['tipo_habitacion'],
            'fecha_ingreso' => new MongoDB\BSON\UTCDateTime(strtotime($data['fecha_ingreso']) * 1000),
            'fecha_salida' => new MongoDB\BSON\UTCDateTime(strtotime($data['fecha_salida']) * 1000),
            'observaciones' => $data['observaciones'],
            'fecha_registro' => new MongoDB\BSON\UTCDateTime(),
            'estado' => $estado
        ];
        
        $resultado = $clientesCollection->insertOne($documento);
        return $resultado->getInsertedCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}
function obtenerHabitacionesOcupadas() {
    global $clientesCollection;

    try {
        // Buscar habitaciones ocupadas donde el estado sea "activo"
        $cursor = $clientesCollection->find(['estado' => 'activo']);
        
        $habitacionesOcupadas = [];
        foreach ($cursor as $documento) {
            $habitacion = [
                'numero' => $documento->habitacion,
                'tipo' => $documento->tipo_habitacion
            ];
            $habitacionesOcupadas[] = $habitacion;
        }
        
        return $habitacionesOcupadas;
    } catch (Exception $e) {
        return [];
    }
}

function obtenerClientes($filtro = null) {
    global $clientesCollection;
    
    try {
        $opciones = [];
        
        if ($filtro == 'activos') {
            $opciones = ['estado' => 'activo'];
        } elseif ($filtro == 'proximos') {
            $fechaLimite = new MongoDB\BSON\UTCDateTime((time() + 86400) * 1000);
            $opciones = [
                'fecha_salida' => ['$lte' => $fechaLimite],
                'estado' => 'activo'
            ];
        }
        
        $cursor = $clientesCollection->find($opciones, ['sort' => ['fecha_registro' => -1]]);
        
        $clientes = [];
        foreach ($cursor as $documento) {
            $cliente = [
                'nombre' => $documento->nombre,
                'documento' => $documento->documento,
                'habitacion' => $documento->habitacion,
                'tipo_habitacion' => $documento->tipo_habitacion,
                'fecha_ingreso' => $documento->fecha_ingreso->toDateTime()->format('Y-m-d H:i:s'),
                'fecha_salida' => $documento->fecha_salida->toDateTime()->format('Y-m-d H:i:s'),
                'observaciones' => $documento->observaciones,
                'estado' => $documento->estado
            ];
            $clientes[] = $cliente;
        }
        
        return $clientes;
    } catch (Exception $e) {
        return [];
    }
}

function eliminarCliente($documento) {
    global $clientesCollection;
    
    try {
        $resultado = $clientesCollection->deleteOne(['documento' => $documento]);
        return $resultado->getDeletedCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function calcularEstado($fechaIngreso, $fechaSalida) {
    $ahora = new DateTime();
    $ingreso = new DateTime($fechaIngreso);
    $salida = new DateTime($fechaSalida);
    
    if ($ahora < $ingreso) return 'pendiente';
    if ($ahora > $salida) return 'finalizado';
    return 'activo';
}

function validarHabitacionDisponible($habitacion, $fechaIngreso, $fechaSalida) {
    global $clientesCollection;
    
    try {
        $fechaIngresoUTC = new MongoDB\BSON\UTCDateTime(strtotime($fechaIngreso) * 1000);
        $fechaSalidaUTC = new MongoDB\BSON\UTCDateTime(strtotime($fechaSalida) * 1000);
        
        $count = $clientesCollection->countDocuments([
            'habitacion' => intval($habitacion),
            'estado' => 'activo',
            '$or' => [
                [
                    'fecha_ingreso' => ['$lte' => $fechaSalidaUTC],
                    'fecha_salida' => ['$gte' => $fechaIngresoUTC]
                ]
            ]
        ]);
        
        return $count == 0;
    } catch (Exception $e) {
        return false;
    }
}

function obtenerEstadisticas() {
    global $clientesCollection;
    
    try {
        $stats = [
            'habitaciones_ocupadas' => 0,
            'total_clientes' => 0,
            'proximos_checkouts' => 0
        ];
        
        // Habitaciones ocupadas
        $stats['habitaciones_ocupadas'] = $clientesCollection->countDocuments(['estado' => 'activo']);
        
        // Total clientes activos
        $stats['total_clientes'] = $clientesCollection->countDocuments(['estado' => 'activo']);
        
        // Próximos checkouts (24 horas)
        $fechaLimite = new MongoDB\BSON\UTCDateTime((time() + 86400) * 1000);
        $stats['proximos_checkouts'] = $clientesCollection->countDocuments([
            'fecha_salida' => ['$lte' => $fechaLimite],
            'estado' => 'activo'
        ]);
        
        return $stats;
    } catch (Exception $e) {
        return [
            'habitaciones_ocupadas' => 0,
            'total_clientes' => 0,
            'proximos_checkouts' => 0
        ];
    }
}
?>

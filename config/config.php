<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoClient = new MongoDB\Client("mongodb+srv://joseluispz:11223344@cluster0.27dbi.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    $database = $mongoClient->selectDatabase('hotel_system');
    $clientesCollection = $database->clientes;
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "Error de conexión con MongoDB: " . $e->getMessage();
    exit;
}
?>
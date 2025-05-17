<?php
header('Content-Type:application/json');
require '../../config/Database.php';
require '../../models/Client.php';
require '../../controllers/api/ClientController.php';
$db =  Database::getInstance()->getConnection();
$controller = new ClientController($db);
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->HandleRequestCreate();
}else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Impossible de contacter le serveur'
    ]);
}
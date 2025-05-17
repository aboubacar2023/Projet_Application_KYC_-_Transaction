<?php
header('Content-Type:application/json');
require '../../config/Database.php';
require '../../models/Client.php';
require '../../controllers/api/ClientController.php';
$db =  Database::getInstance()->getConnection();
$controller = new ClientController($db);
if(isset($_GET["id"])) {
    $telephone = $_GET["id"];
    $controller->HandleRequestProfil($telephone);
}else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Impossible de contacter le serveur'
    ]);
}
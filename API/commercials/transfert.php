<?php
header('Content-Type:application/json');
require '../../config/Database.php';
require '../../models/Operation.php';
require '../../controllers/api/OperationController.php';
$db =  Database::getInstance()->getConnection();
$controller = new OperationController($db);
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->HandleRequestTransfert();
}else {
    http_response_code(404);
    echo json_encode([
        'error' => 'Impossible de contacter le serveur'
    ]);
}
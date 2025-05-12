<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

// if (!$auth->isLoggedIn()) {
//     header('Location: ../login.php');
//     exit;
// }
// $db = Database::getInstance()->getConnection();
// $sqlQuery = 'SELECT * FROM clients id = :id';
// $query = $db->prepare($sqlQuery);
// $query->execute([
//     // 'id' => 
// ]);
// $clients = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Tranferts</h1>
    </div>

    <div class="bg-dark-200 rounded-lg border border-dark-300 overflow-hidden mb-6">
        <div class="overflow-x-auto">

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_client.php';
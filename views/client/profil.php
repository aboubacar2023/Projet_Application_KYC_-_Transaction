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
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2x3 font-bold">Vos Informations</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium text-gray-100">Téléphone</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Prénom</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Nom</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Email</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Adresse</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date de Naissance</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 mb-3 border rounded">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date Création Compte</label>
                    <input type="text" placeholder="Votre Contact" readonly class="w-full mt-3 p-2 border rounded">
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_client.php';
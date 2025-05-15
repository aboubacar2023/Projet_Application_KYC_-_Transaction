<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}
$db = Database::getInstance()->getConnection();
$telephone = $_SESSION['user']['telephone'];
$sqlQuery = 'SELECT * FROM clients WHERE telephone = :telephone';
$query = $db->prepare($sqlQuery);
$query->execute([
    'telephone' => $telephone
]);
$clients = $query->fetch();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Vos Informations</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="grid grid-cols-2 gap-y-3 gap-x-10">
                <div>
                    <label class="text-sm font-medium text-gray-100">Téléphone</label>
                    <input type="text" placeholder="<?= $clients['telephone'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Prénom</label>
                    <input type="text" placeholder="<?= $clients['prenom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Nom</label>
                    <input type="text" placeholder="<?= $clients['nom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Email</label>
                    <input type="text" placeholder="<?= $clients['email'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Adresse</label>
                    <input type="text" placeholder="<?= $clients['adresse'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <?php
                    $date_naissance = new DateTime($clients['date_naissance']);
                    $date_creation = new DateTime($clients['date_creation']) 
                ?>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date de Naissance</label>
                    <input type="text" placeholder="<?= $date_naissance->format('d-m-Y') ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date Création Compte</label>
                    <input type="text" placeholder="<?= $date_creation->format('d-m-Y à H:i') ?>" readonly
                        class="w-full mt-3 p-2 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Solde</label>
                    <input type="text" placeholder="<?= $clients['solde'] ?> FCFA" readonly
                        class="w-full mt-3 p-2 border rounded font-bold">
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_client.php';
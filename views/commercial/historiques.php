<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->verificationNiveau()) {
    header('Location: ../login.php');
    exit();
}
// Récuperation des clients en fonction du niveau de vérification
$id = $_SESSION['user']['id'];
$db = Database::getInstance()->getConnection();
$sqlQuery = 'SELECT telephone, prenom, nom, montant, type_operation, date_operation FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    WHERE id_commercial = ?
    AND validation_operation = ?
';
$query = $db->prepare($sqlQuery);
$query->execute([
    $id,
    true
]);
$operations = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Historique des opérations</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-300">
                <thead class="bg-dark-300">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Contact</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Prénom & Nom</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Type Opération</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Montant</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-300">
                    <?php foreach ($operations as $operation) {?>
                    <tr class="hover:bg-dark-300">
                        <td class="px-6 py-4 whitespace-nowrap"><?= $operation['telephone'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $operation['prenom'] ?> <?= $operation['nom'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= ucfirst($operation['type_operation']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $operation['montant'] ?></td>
                        <?php
                            $date = new DateTime($operation['date_operation']);
                        ?>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $date->format('d-m-Y'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_commercial.php';
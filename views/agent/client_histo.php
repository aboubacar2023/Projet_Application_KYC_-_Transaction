<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}
// Récuperation des clients en fonction du niveau de vérification
$role = $_SESSION['user']['role'];
// ID de l'utilisateur (l'agent)
$id = $_SESSION['user']['id'];

$db = Database::getInstance()->getConnection();
switch ($role) {
    case 'agent1':
        $sqlQuery = "SELECT telephone, c.prenom, c.nom FROM clients AS c 
            JOIN verifications AS v ON c.telephone = v.telephone_client 
            JOIN utilisateurs AS u ON v.id_agent = u.id 
            -- revenir sur cette partie pour que l'agent ne voit que les clients qu'il a validé
            WHERE niveau = 1
            AND u.id = :id
        ";
        break;
    case 'agent2':
        $sqlQuery = "SELECT telephone, c.prenom, c.nom FROM clients AS c 
            JOIN verifications AS v ON c.telephone = v.telephone_client 
            JOIN utilisateurs AS u ON v.id_agent = u.id 
            WHERE niveau = 2
            AND u.id = :id
        ";
        break;
}
$query = $db->prepare($sqlQuery);
$query->execute([
    'id' => $id
]);
$clients = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Historique des vérifications</h1>
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
                            Prénom</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Nom</th>
                        <th scope="col"
                            class="pl-4 pr-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-300">
                    <?php foreach ($clients as $client) {?>
                    <tr class="hover:bg-dark-300">
                        <td class="px-6 py-4 whitespace-nowrap"><?= $client['telephone']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $client['prenom']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $client['nom']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end" style="padding-left: -30px;">
                                <a href="client_histo_indiv.php?id=<?= $client['telephone'] ?>">
                                    <button class="text-blue-400 hover:text-blue-500">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_agent.php';
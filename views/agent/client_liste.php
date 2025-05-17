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
$role = $_SESSION['user']['role'];
$db = Database::getInstance()->getConnection();
switch ($role) {
    case 'agent1':
        $sqlQuery = "SELECT telephone, prenom, nom FROM clients WHERE statut = 'en_attente'";
        break;
    case 'agent2':
        $sqlQuery = "SELECT telephone, prenom, nom FROM clients WHERE statut = 'verifie_niv1'";
        break;
}
$query = $db->prepare($sqlQuery);
$query->execute();
$clients = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Liste des clients à vérifier</h1>
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
                                <a href="client_liste_indiv.php?id=<?= $client['telephone'] ?>">
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
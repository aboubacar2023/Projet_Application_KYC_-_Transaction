<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->verificationNiveau()) {
    header('Location: ../login.php');
    exit();
}
// Récuperation des utilisateurs
$db = Database::getInstance()->getConnection();
$sqlQuery = "SELECT * FROM utilisateurs";
$query = $db->prepare($sqlQuery);
$query->execute();
$users = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Liste des utilisateurs</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-dark-300">
                <thead class="bg-dark-300">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Prénom</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Nom</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Rôle</th>
                        <th scope="col"
                            class="pl-4 pr-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-300">
                    <?php foreach ($users as $user) {?>
                    <tr class="hover:bg-dark-300">
                        <td class="px-6 py-4 whitespace-nowrap"><?= $user['prenom']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $user['nom']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo ucfirst($user['role'] ?? 'Non défini') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end" style="padding-left: -30px;">
                                <a href="utilisateur_indiv.php?id=<?= $user['id'] ?>">
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
include 'base_admin.php';
<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn() || !$auth->verificationNiveau()) {
    header('Location: ../login.php');
    exit();
}
$db = Database::getInstance()->getConnection();
$id = $_GET['id'];
$sqlQuery = "SELECT * FROM utilisateurs WHERE id = :id";
$query = $db->prepare($sqlQuery);
$query->execute([
    'id' => $id
]);
$user = $query->fetch();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Informations de l'utilisateur</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="grid grid-cols-2 gap-y-3 gap-x-10 mb-8">
                <div>
                    <label class="text-sm font-medium text-gray-100">Prénom</label>
                    <input type="text" placeholder="<?= $user['prenom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Nom</label>
                    <input type="text" placeholder="<?= $user['nom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Niveau</label>
                    <input type="text" placeholder="<?php echo ucfirst($user['role'] ?? 'Non défini') ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
            </div>
            <form action="../../controllers/AdminController.php" method="POST">
                <h1 class="text-2xl font-bold">Modification du Niveau</h1>

                <div class="grid grid-cols-2 gap-y-3 gap-x-10 mb-4 mt-2">
                    <input type="text" name="id" hidden value="<?= $user['id'] ?>">
                    <div>
                        <label for="role" class="text-sm font-medium text-gray-100 pb-4">Niveau</label>
                        <select type="text" name="role" required
                            class="text-black block w-full p-2 mt-3 border rounded">
                            <option value="" disabled selected hidden>Sélectionnez le niveau</option>
                            <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>
                                class="text-black">Admin</option>
                            <option value="agent1" <?php echo ($user['role'] ?? '') === 'agent1' ? 'selected' : ''; ?>
                                class="text-black">Agent 1</option>
                            <option value="agent2" <?php echo ($user['role'] ?? '') === 'agent2' ? 'selected' : ''; ?>
                                class="text-black">Agent 2</option>
                            <option value="commercial"
                                <?php echo ($user['role'] ?? '') === 'commercial' ? 'selected' : ''; ?>
                                class="text-black">
                                Commercial</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-center pt-6 pb-4">
                    <button type="submit" class="w-1/4 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded ">
                        <?php 
                            if (isset($user['role'])) {
                                echo 'Modifier';
                            } else {
                                echo 'Enregistrer';
                            }
                        
                        ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_admin.php';
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
$telephone = $_GET['id'];
$sqlQuery = 'SELECT * FROM clients JOIN documents ON clients.telephone = documents.telephone_client WHERE telephone = :telephone';
$query = $db->prepare($sqlQuery);
$query->execute([
    'telephone' => $telephone
]);
$client = $query->fetch();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Première Validation</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="grid grid-cols-2 gap-y-3 gap-x-10 mb-8">
                <div>
                    <label class="text-sm font-medium text-gray-100">Téléphone</label>
                    <input type="text" placeholder="<?= $client['telephone'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Prénom</label>
                    <input type="text" placeholder="<?= $client['prenom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Nom</label>
                    <input type="text" placeholder="<?= $client['nom'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Email</label>
                    <input type="text" placeholder="<?= $client['email'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Adresse</label>
                    <input type="text" placeholder="<?= $client['adresse'] ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date de Naissance</label>
                    <?php
                        $date_naissance = new DateTime($client['date_naissance']);
                        $date_creation = new DateTime($client['date_creation']) 
                    ?>
                    <input type="text" placeholder="<?= $date_naissance->format('d-m-Y') ?>" readonly
                        class="w-full mt-3 p-2 mb-3 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Date Création Compte</label>
                    <input type="text" placeholder="<?= $date_creation->format('d-m-Y à H:i') ?>" readonly
                        class="w-full mt-3 p-2 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Type Document</label>
                    <input type="text" placeholder="<?= $client['type_document'] ?>" readonly
                        class="w-full mt-3 p-2 border rounded font-bold">
                </div>
                <div>
                    <h2 class="text-xl font-medium text-gray-100 mb-6">Document</h2>
                    <?php 
                    $chemin = '../../uploads/'.$client['chemin_fichier'];
                    if (file_exists($chemin)) {
                        // Ouvre le fichier en lecture
                        echo "<a href='$chemin' target='_blank' class='border rounded px-3 py-3 mb-6 hover:bg-gray-100 hover:text-black'>
                                Voir le document
                            </a>";
                    } ?>
                </div>
            </div>
            <form action="../../controllers/AgentController.php" method="POST">
                <div class="grid grid-cols-2 gap-y-3 gap-x-10 mb-4">
                    <input type="text" name="telephone" hidden value="<?= $client['telephone'] ?>">
                    <div>
                        <label for="validation" class="text-sm font-medium text-gray-100 pb-4">Validation
                            Inscription</label>
                        <select type="text" name="validation" required
                            class="text-black block w-full p-2 mt-3 border rounded">
                            <option value="" disabled selected hidden>Sélectionnez le Type de document</option>
                            <option value="validé" class="text-black">Valider</option>
                            <option value="rejeté" class="text-black">Rejeter</option>
                        </select>
                    </div>
                    <div>
                        <label for='commentaire' class="text-sm font-medium text-gray-100">Commentaire</label>
                        <input type="text" name="commentaire" required
                            class="text-black w-full mt-3 p-2 border rounded font-bold">
                    </div>
                </div>
                <div class="flex justify-center pt-6 pb-4">
                    <button type="submit"
                        class="w-1/4 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded ">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_agent.php';
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
// ID de l'utilisateur (l'agent)
$id = $_SESSION['user']['id'];
$sqlQuery = 'SELECT * FROM clients AS c
    JOIN documents AS d ON c.telephone = d.telephone_client 
    JOIN verifications AS v ON c.telephone = v.telephone_client 
    JOIN utilisateurs AS u ON v.id_agent = u.id
    WHERE u.id = :id
    AND telephone = :telephone';
$query = $db->prepare($sqlQuery);
$query->execute([
    'id' => $id,
    'telephone' => $telephone
]);
$client = $query->fetch();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Historique</h1>
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
                    $chemin = '../../upload/'.$client['chemin_fichier'];
                    if (file_exists($chemin)) {
                        // Ouvre le fichier en lecture
                        echo "<a href='$chemin' target='_blank' class='border rounded px-3 py-3 mb-6 hover:bg-gray-100 hover:text-black'>
                                Voir le document
                            </a>";
                    } ?>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-y-3 gap-x-10 mb-4">
                <div>
                    <label class="text-sm font-medium text-gray-100 pb-4">Statut Validation</label>
                    <?php 
                        switch ($client['statut']) {
                            case 'verifie_niv1':
                                $validation = "Validé";
                                break;
                            case 'verifie_total':
                                $validation = "Validé";
                                break;
                            default:
                                $validation = "Rejeté";
                                break;
                        }
                    ?>
                    <input type="text" placeholder="<?= $validation ?>" readonly
                        class="text-black w-full mt-3 p-2 border rounded font-bold">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-100">Commentaire</label>
                    <input type="text" placeholder="<?= $client['commentaire'] ?>" readonly
                        class="text-black w-full mt-3 p-2 border rounded font-bold">
                </div>
                <div>
                    <?php
                        $date_verification = new DateTime($client['date_verification']);
                    ?>
                    <label class="text-sm font-medium text-gray-100">Date Vérification</label>
                    <input type="text" placeholder="<?= $date_verification->format('d-m-Y') ?>" readonly
                        class="text-black w-full mt-3 p-2 border rounded font-bold">
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_agent.php';
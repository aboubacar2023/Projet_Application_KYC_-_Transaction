<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}
// Recuperation des cas de transfert
$user = $_SESSION['user']['telephone'];
$db = Database::getInstance()->getConnection();
$sqlQuery = 'SELECT o.id, montant, u.prenom, u.nom FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    JOIN utilisateurs AS u ON o.id_commercial = u.id
    WHERE telephone_client = ?
    AND validation_operation = ?
';
$query = $db->prepare($sqlQuery);
$query->execute([
    $user,
    false
]);
$retrait = $query->fetch();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Les Tranferts d'argents</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <?php if (!empty($retrait)) { ?>
            <form class="mb-10" action="../../controllers/ClientController.php" method="POST">
                <p class="text-xl mb-4">Veuillez confirmer le retrait de <?= $retrait['montant'] ?> FCFA du commercial
                    <?= $retrait['prenom'] ?> <?= $retrait['nom'] ?>
                </p>
                <input type="text" hidden name="action" value="retrait">
                <input type="text" hidden name="montant" value="<?=$retrait['montant']?>">
                <input type="text" hidden name="id_operation" value="<?=$retrait['id']?>">

                <label class="text-sm font-medium text-gray-100 " for='confirmation'>Confirmation du
                    retrait</label>
                <select type="text" name="confirmation" required
                    class="text-black mt-4 block w-1/3 p-2 mb-4 border rounded">
                    <option value="" class="text-black"></option>
                    <option value="Oui" class="text-black">Oui</option>
                    <option value="Non" class="text-black">Non</option>
                </select>
                <button type="submit"
                    class="w-1/4 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded">Enregistrer</button>

            </form> <?php } ?>
            <!-- Une section qui va s'afficher quand on doit confirmer un retrait -->
            <form action=" ../../controllers/ClientController.php" method="POST">
                <!-- Pour les retraits -->
                <?php if (isset($_SESSION['erreur_retrait'])) {
                    echo '<p class="text-red-500 pb-3">' . htmlspecialchars($_SESSION['erreur_retrait']) . '</p>';
                    unset($_SESSION['erreur_retrait']); 
                } ?>
                <?php if (isset($_SESSION['success_retrait'])) {
                    echo '<p class="text-green-500 pb-3">' . htmlspecialchars($_SESSION['success_retrait']) . '</p>';
                    unset($_SESSION['success_retrait']); 
                } ?>
                <!-- Pour les transferts -->
                <?php if (isset($_SESSION['erreur_transfert'])) {
                    echo '<p class="text-red-500 pb-3">' . htmlspecialchars($_SESSION['erreur_transfert']) . '</p>';
                    unset($_SESSION['erreur_transfert']); 
                } ?>
                <?php if (isset($_SESSION['success_transfert'])) {
                    echo '<p class="text-green-500 pb-3">' . htmlspecialchars($_SESSION['success_transfert']) . '</p>';
                    unset($_SESSION['success_transfert']); 
                } ?>
                <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                    <input type="text" hidden name="action" value="transfert">
                    <div>
                        <label class="text-sm font-medium text-gray-100" name='telephone'>Numéro qui recevra le
                            transfert</label>
                        <input type="text" placeholder="70707070" required minlength="8" maxlength="8"
                            class="text-black w-full mt-3 p-2 mb-3 border rounded font-bold" name='telephone'>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-100" name='montant'>Montant à transférer
                            (FCFA)</label>
                        <input type="text" placeholder="5000" required
                            class="text-black w-full mt-3 p-2 mb-3 border rounded font-bold" name='montant'>
                    </div>
                </div>
                <div class="flex justify-center pt-3">
                    <button type="submit"
                        class="w-1/4 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded ">Valider</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_client.php';
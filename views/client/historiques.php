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
// On fera une requete pour chaque type d'opérations (depot, retrait, transfert_entrant, transfert_sortant)

$telephone = $_SESSION['user']['telephone'];
$db = Database::getInstance()->getConnection();
// depot
$sqlQuery = "SELECT u.prenom, u.nom, montant, date_operation FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    JOIN utilisateurs AS u ON o.id_commercial = u.id
    WHERE telephone_client = ?
    AND type_operation = 'depot' 
    AND validation_operation = ?
";
$query = $db->prepare($sqlQuery);
$query->execute([
    $telephone,
    true
]);
$depots = $query->fetchAll();
// retrait
$sqlQuery = "SELECT u.prenom, u.nom, montant, date_operation FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    JOIN utilisateurs AS u ON o.id_commercial = u.id
    WHERE telephone_client = ?
    AND type_operation = 'retrait' 
    AND validation_operation = ?
";
$query = $db->prepare($sqlQuery);
$query->execute([
    $telephone,
    true
]);
$retraits = $query->fetchAll();
// transfert_entrant
$sqlQuery = "SELECT telephone_client, c.prenom, c.nom, montant, date_operation 
    FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    JOIN clients AS c2 ON o.telephone_destinataire = c2.telephone
    WHERE telephone_destinataire = ?
    AND type_operation = 'transfert_entrant' 
    AND validation_operation = ?
";
$query = $db->prepare($sqlQuery);
$query->execute([
    $telephone,
    true
]);

$entrants = $query->fetchAll();
// transfert_sortant
$sqlQuery = "SELECT telephone_destinataire, c2.prenom, c2.nom, montant, date_operation 
    FROM clients AS c 
    JOIN operations AS o ON c.telephone = o.telephone_client
    JOIN clients AS c2 ON o.telephone_destinataire = c2.telephone
    WHERE telephone_client = ?
    AND type_operation = 'transfert_sortant' 
    AND validation_operation = ?
";
$query = $db->prepare($sqlQuery);
$query->execute([
    $telephone,
    true
]);
$sortants = $query->fetchAll();
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Historique des opérations</h1>
    </div>
    <div class="flex space-x-4 mb-6 border-b border-gray-300">
        <button id="btn-depot" class="filter-btn relative text-gray-600 font-medium py-2 px-4 transition">Dépôt</button>
        <button id="btn-retrait"
            class="filter-btn relative text-gray-600 font-medium py-2 px-4 transition">Retrait</button>
        <button id="btn-entrant" class="filter-btn relative text-gray-600 font-medium py-2 px-4 transition">Transfert
            Entrant</button>
        <button id="btn-sortant" class="filter-btn relative text-gray-600 font-medium py-2 px-4 transition">Transfert
            Sortant</button>
    </div>


    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="content_attribut hidden" id="content-depot">
                <table class="min-w-full divide-y divide-dark-300">
                    <thead class="bg-dark-300">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Commercial</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Montant</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-300">
                        <?php foreach ($depots as $depot) {?>
                        <tr class="hover:bg-dark-300">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $depot['prenom'] ?> <?= $depot['nom'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $depot['montant'] ?></td>
                            <?php
                            $date = new DateTime($depot['date_operation']);
                        ?>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $date->format('d-m-Y'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="content_attribut hidden" id="content-retrait">
                <table class="min-w-full divide-y divide-dark-300">
                    <thead class="bg-dark-300">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Commercial</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Montant</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-300">
                        <?php foreach ($retraits as $retrait) {?>
                        <tr class="hover:bg-dark-300">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $retrait['prenom'] ?> <?= $retrait['nom'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $retrait['montant'] ?></td>
                            <?php
                            $date = new DateTime($retrait['date_operation']);
                        ?>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $date->format('d-m-Y'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="content_attribut hidden" id="content-entrant">
                <table class="min-w-full divide-y divide-dark-300">
                    <thead class="bg-dark-300">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Contact Expéditeur</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Expéditeur</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Montant</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-300">
                        <?php foreach ($entrants as $entrant) {?>
                        <tr class="hover:bg-dark-300">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $entrant['telephone_client'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $entrant['prenom'] ?> <?= $entrant['nom'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $entrant['montant'] ?></td>
                            <?php
                            $date = new DateTime($entrant['date_operation']);
                        ?>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $date->format('d-m-Y'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="content_attribut hidden" id="content-sortant">
                <table class="min-w-full divide-y divide-dark-300">
                    <thead class="bg-dark-300">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Contact Destinataire</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Destinataire</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Montant</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-300">
                        <?php foreach ($sortants as $sortant) {?>
                        <tr class="hover:bg-dark-300">
                            <td class="px-6 py-4 whitespace-nowrap"><?= $sortant['telephone_destinataire'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $sortant['prenom'] ?> <?= $sortant['nom'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $sortant['montant'] ?></td>
                            <?php
                            $date = new DateTime($sortant['date_operation']);
                        ?>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $date->format('d-m-Y'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_client.php';
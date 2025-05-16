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

// 1. Nombre total de clients
$totalClients = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();

// 2. Nombre total d’utilisateurs
$totalUsers = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();

// 3. Nombre total de transactions
$totalTransactions = $db->query("SELECT COUNT(*) FROM operations")->fetchColumn();

// 4. Nombre par type de transaction
$stmt = $db->query("
    SELECT 
        type_operation, 
        COUNT(*) AS total,
        SUM(montant) AS somme 
    FROM operations 
    WHERE validation_operation = 1
    GROUP BY type_operation
");

$stats = [
    'depot' => ['total' => 0, 'somme' => 0],
    'retrait' => ['total' => 0, 'somme' => 0],
    'transfert_entrant' => ['total' => 0, 'somme' => 0],
    'transfert_sortant' => ['total' => 0, 'somme' => 0],
];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $type = $row['type_operation'];
    if (isset($stats[$type])) {
        $stats[$type]['total'] = $row['total'];
        $stats[$type]['somme'] = $row['somme'];
    }
}
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-2xl font-bold">Tableau de Bord Administratif</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <div class="box">
                <h2>Statistiques Générales</h2>
                <p><strong>Nombre total de clients :</strong> <?= $totalClients ?></p>
                <p><strong>Nombre total d’utilisateurs :</strong> <?= $totalUsers ?></p>
                <p><strong>Nombre total de transactions :</strong> <?= $totalTransactions ?></p>
            </div>
            <div class="box">
                <h2>Détails des Transactions</h2>
                <p><strong>Dépôts :</strong> <?= $stats['depot']['total'] ?> opérations —
                    <?= $stats['depot']['somme'] ?> FCFA
                </p>
                <p><strong>Retraits :</strong> <?= $stats['retrait']['total'] ?> opérations —
                    <?= $stats['retrait']['somme'] ?>
                    FCFA</p>
                <p><strong>Transferts entrants :</strong> <?= $stats['transfert_entrant']['total'] ?> —
                    <?= $stats['transfert_entrant']['somme'] ?> FCFA</p>
                <p><strong>Transferts sortants :</strong> <?= $stats['transfert_sortant']['total'] ?> —
                    <?= $stats['transfert_sortant']['somme'] ?> FCFA</p>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_admin.php';
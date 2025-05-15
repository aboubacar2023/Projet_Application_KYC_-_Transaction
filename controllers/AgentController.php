<?php
session_start();
require __DIR__ . '/../config/Database.php';

$db = Database::getInstance()->getConnection();
$validation = $_POST['validation'];
$telephone_client = $_POST['telephone'];
$commentaire = $_POST['commentaire'];
$id_agent = $_SESSION['user']['id'];
$niveau = explode('t', $_SESSION['user']['role'])[1];

$query = "INSERT INTO verifications (telephone_client, niveau, id_agent, commentaire) VALUES (?, ?, ?, ?)";
$insertion = $db->prepare($query);
$insertion->execute([
    $telephone_client,
    $niveau,
    $id_agent,
    $commentaire
]);
if ($validation === 'validé') {
    // Chaque agent peut utiliser cette partie, mais ici on doit faire la difference et agir en consequence
    if ($niveau === '1') {
        $statut = 'verifie_niv1';
    } else {
        $statut = 'verifie_total';
    }
} else {
    $statut = 'rejeté';
}



$sqlQuery = 'UPDATE clients SET statut = :statut WHERE telephone = :telephone';
$query = $db->prepare($sqlQuery);
$query->execute([
    'statut' => $statut,
    'telephone' => $telephone_client
]);

header('Location: ../views/agent/client_liste.php');
exit();
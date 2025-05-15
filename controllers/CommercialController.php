<?php
session_start();
require __DIR__ . '/../config/Database.php';

function saveDepot() {
    // On verifie si le contact est sur la base de donnée et aussi si le motant du client est capable
    $db = Database::getInstance()->getConnection();
    $telephone_receveur = trim($_POST['telephone']);
    $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
    $query = $db->prepare($sqlQuery);
    $query->execute([
        'telephone' => $telephone_receveur
    ]);
    $client = $query->fetch();
    if (!empty($client)) {
        $id_commercial = $_SESSION['user']['id'];
        $montant = (int)$_POST['montant'];
        $solde_client_reception = $client['solde'];
        if ($montant > 0) {
            // montant à rajouter
            $montant_rajout = $solde_client_reception + $montant;
            $sqlQuery1 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
            $query1 = $db->prepare($sqlQuery1);
            $query1->execute([
                'solde' => $montant_rajout,
                'telephone' => $telephone_receveur
            ]);
            // Dans la table intermediaire opérations pour le telephone_client
            $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, id_commercial) VALUES (?, 'depot', ?, ?)";
            $query3 = $db->prepare($sqlQuery3);
            $query3->execute([
                $telephone_receveur,
                $montant,
                $id_commercial
            ]);
            $_SESSION['success_depot'] = "Dépot effectué avec succès";
        } else {
            $_SESSION['erreur_depot'] = "Saisissez une somme supérieure à 0";
        }
    } else {
        $_SESSION['erreur_depot'] = "Ce contact n'a pas de compte sur l'application !!!";
    }
    header('Location: ../views/commercial/depot.php');
    exit();
    
}

function saveRetrait() {
    // On verifie si le contact est sur la base de donnée et aussi si le motant du client est capable
    $db = Database::getInstance()->getConnection();
    $telephone_acteur = trim($_POST['telephone']);
    $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
    $query = $db->prepare($sqlQuery);
    $query->execute([
        'telephone' => $telephone_acteur
    ]);
    $client = $query->fetch();
    if (!empty($client)) {
        $id_commercial = $_SESSION['user']['id'];
        $solde_acteur = $client['solde'];
        $montant = (int)$_POST['montant'];
        if ($solde_acteur >= $montant) {
            // Dans la table intermediaire opérations pour le telephone_client
            $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, id_commercial, validation_operation) VALUES (?, 'retrait', ?, ?, ?)";
            $query3 = $db->prepare($sqlQuery3);
            $query3->execute([
                $telephone_acteur,
                $montant,
                $id_commercial,
                false
            ]);
            $_SESSION['success_retrait'] = "Le client doit confimer le retrait";
        } else {
            $_SESSION['erreur_retrait'] = "Le solde du client est insuffisant pour ce retrait !!!";
        }
    } else {
        $_SESSION['erreur_retrait'] = "Ce contact n'a pas de compte sur l'application !!!";
    }
    header('Location: ../views/commercial/retrait.php');
    exit();
    
}


switch ($_POST['action']) {
    case 'depot':
        saveDepot();
        break;
    default:
        saveRetrait();
        break;
}
<?php
session_start();
require __DIR__ . '/../config/Database.php';

function saveTransfert() {
    // On verifie si le contact est sur la base de donnée et aussi si le motant du client est capable
    $db = Database::getInstance()->getConnection();
    $telephone_receveur = trim($_POST['telephone']);
    $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
    $query = $db->prepare($sqlQuery);
    $query->execute([
        'telephone' => $telephone_receveur
    ]);
    $client = $query->fetch();
    if(!empty($client['telephone']) && $_SESSION['user']['telephone'] === $_POST['telephone']) {
        $_SESSION['erreur_transfert'] = "Vous ne pouvez pas vous transférer de l'argent !!!";
    } elseif (!empty($client)) {
        $sqlQuery = "SELECT solde FROM clients WHERE telephone = :telephone";
        $query = $db->prepare($sqlQuery);
        $query->execute([
            'telephone' => $_SESSION['user']['telephone']
        ]);
        $data = $query->fetch();
        $solde_acteur = $data['solde'];
        $montant = (int)$_POST['montant'];
        $solde_client_reception = $client['solde'];
        if ($solde_acteur >= $montant) {
            // montant à rajouter
            $new_montant = $solde_client_reception + $montant;
            $sqlQuery1 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
            $query1 = $db->prepare($sqlQuery1);
            $query1->execute([
                'solde' => $new_montant,
                'telephone' => $telephone_receveur
            ]);
            // Montant à enlever
            $new_montant = $solde_acteur - $montant;
            $sqlQuery2 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
            $query2 = $db->prepare($sqlQuery2);
            $query2->execute([
                'solde' => $new_montant,
                'telephone' => $_SESSION['user']['telephone']
            ]);
            // Dans la table intermediaire opérations pour le telephone_client
            $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, telephone_destinataire) VALUES (?, 'transfert_sortant', ?, ?)";
            $query3 = $db->prepare($sqlQuery3);
            $query3->execute([
                $_SESSION['user']['telephone'],
                $montant,
                $telephone_receveur
            ]);
            // Dans la table intermediaire opérations pour le telephone_destinataire
            $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, telephone_destinataire) VALUES (?, 'transfert_entrant', ?, ?)";
            $query3 = $db->prepare($sqlQuery3);
            $query3->execute([
                $_SESSION['user']['telephone'],
                $montant,
                $telephone_receveur
            ]);
            $_SESSION['success_transfert'] = "Transfert effectué avec succès";
        } else {
            $_SESSION['erreur_transfert'] = "Votre solde est insuffisant pour ce transfert !!!";
        }
    } else {
        $_SESSION['erreur_transfert'] = "Ce contact n'a pas de compte sur l'application !!!";
    }
    header('Location: ../views/client/transferts.php');
    exit();
    
}

function saveRetrait() {
    if ($_POST['confirmation'] === 'Oui') {
        $db = Database::getInstance()->getConnection();
        $montant = (int)$_POST['montant'];
        $id_operation = $_POST['id_operation'];
        $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone";
        $query = $db->prepare($sqlQuery);
        $query->execute([
            'telephone' => $_SESSION['user']['telephone']
        ]);
        $client = $query->fetch();
        
        // Montant à enlever
        $new_montant = $client['solde'] - $montant;
        $sqlQuery = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
        $query = $db->prepare($sqlQuery);
        $query->execute([
            'solde' => $new_montant,
            'telephone' => $_SESSION['user']['telephone']
        ]);
        // Validation du retrait dans la table operation
        $sqlQuery1 = 'UPDATE operations SET validation_operation = ? WHERE id = ?';
        $query1 = $db->prepare($sqlQuery1);
        $query1->execute([
            true,
            $id_operation
        ]);
        $_SESSION['success_retrait'] = "Retrait validé avec succès";
    } else {
        $db = Database::getInstance()->getConnection();
        $id_operation = $_POST['id_operation'];
        $sqlQuery = 'DELETE FROM operations WHERE id = ?';
        $query = $db->prepare($sqlQuery);
        $query->execute([
            $id_operation
        ]);
        $_SESSION['erreur_retrait'] = "Votre retrait a été annulé !!!";
    }
    header('Location: ../views/client/transferts.php');
    exit();
    
}

switch ($_POST['action']) {
    case 'transfert':
        saveTransfert();
        break;
    case 'retrait':
        saveRetrait();
        break;
}
<?php
class Operation{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function depot() {
        $telephone_receveur = trim($_POST['telephone']);
        $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
        $query = $this->pdo->prepare($sqlQuery);
        $query->execute([
            'telephone' => $telephone_receveur
        ]);
        $client = $query->fetch();
        if (!empty($client)) {
            $id_commercial = $_POST['id_commercial'];
            $montant = (int)$_POST['montant'];
            $solde_client_reception = $client['solde'];
            if ($montant > 0) {
                // montant à rajouter
                $montant_rajout = $solde_client_reception + $montant;
                $sqlQuery1 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
                $query1 = $this->pdo->prepare($sqlQuery1);
                $query1->execute([
                    'solde' => $montant_rajout,
                    'telephone' => $telephone_receveur
                ]);
                // Dans la table intermediaire opérations pour le telephone_client
                $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, id_commercial) VALUES (?, 'depot', ?, ?)";
                $query3 = $this->pdo->prepare($sqlQuery3);
                $query3->execute([
                    $telephone_receveur,
                    $montant,
                    $id_commercial
                ]);
                return "Dépot effectué avec succès";
            } else {
                return "Saisissez une somme supérieure à 0";
            }
        } else {
            return "Ce contact n'a pas de compte sur l'application !!!";
        }
    }
    public function retrait() {
        $telephone_acteur = trim($_POST['telephone']);
        $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
        $query = $this->pdo->prepare($sqlQuery);
        $query->execute([
            'telephone' => $telephone_acteur
        ]);
        $client = $query->fetch();
        if (!empty($client)) {
            $id_commercial = $_POST['id_commercial'];
            $solde_acteur = $client['solde'];
            $montant = (int)$_POST['montant'];
            if ($solde_acteur >= $montant) {
                // Dans la table intermediaire opérations pour le telephone_client
                $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, id_commercial, validation_operation) VALUES (?, 'retrait', ?, ?, ?)";
                $query3 = $this->pdo->prepare($sqlQuery3);
                $query3->execute([
                    $telephone_acteur,
                    $montant,
                    $id_commercial,
                    false
                ]);
                return "Le client doit confimer le retrait. L'opération a été enregistré";
            } else {
                return "Le solde du client est insuffisant pour ce retrait !!!";
            }
        } else {
            return "Ce contact n'a pas de compte sur l'application !!!";
        }
    }

    public function transfert() {
        $telephone_receveur = trim($_POST['telephone']);
        $sqlQuery = "SELECT * FROM clients WHERE telephone = :telephone AND statut = 'verifie_total'";
        $query = $this->pdo->prepare($sqlQuery);
        $query->execute([
            'telephone' => $telephone_receveur
        ]);
        $client = $query->fetch();
        if(!empty($client['telephone']) && $_POST['telephone_expediteur'] === $_POST['telephone']) {
            return "Vous ne pouvez pas vous transférer de l'argent !!!";
        } elseif (!empty($client)) {
            $sqlQuery = "SELECT solde FROM clients WHERE telephone = :telephone";
            $query = $this->pdo->prepare($sqlQuery);
            $query->execute([
                'telephone' => $_POST['telephone_expediteur']
            ]);
            $data = $query->fetch();
            $solde_acteur = $data['solde'];
            $montant = (int)$_POST['montant'];
            $solde_client_reception = $client['solde'];
            if ($solde_acteur >= $montant) {
                // montant à rajouter
                $new_montant = $solde_client_reception + $montant;
                $sqlQuery1 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
                $query1 = $this->pdo->prepare($sqlQuery1);
                $query1->execute([
                    'solde' => $new_montant,
                    'telephone' => $telephone_receveur
                ]);
                // Montant à enlever
                $new_montant = $solde_acteur - $montant;
                $sqlQuery2 = 'UPDATE clients SET solde = :solde WHERE telephone = :telephone';
                $query2 = $this->pdo->prepare($sqlQuery2);
                $query2->execute([
                    'solde' => $new_montant,
                    'telephone' => $_POST['telephone_expediteur']
                ]);
                // Dans la table intermediaire opérations pour le telephone_client
                $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, telephone_destinataire) VALUES (?, 'transfert_sortant', ?, ?)";
                $query3 = $this->pdo->prepare($sqlQuery3);
                $query3->execute([
                    $_POST['telephone_expediteur'],
                    $montant,
                    $telephone_receveur
                ]);
                // Dans la table intermediaire opérations pour le telephone_destinataire
                $sqlQuery3 = "INSERT INTO operations (telephone_client, type_operation, montant, telephone_destinataire) VALUES (?, 'transfert_entrant', ?, ?)";
                $query3 = $this->pdo->prepare($sqlQuery3);
                $query3->execute([
                    $_POST['telephone_expediteur'],
                    $montant,
                    $telephone_receveur
                ]);
                return "Transfert effectué avec succès";
            } else {
            return "Votre solde est insuffisant pour ce transfert !!!";
            }
        } else {
        return "Ce contact n'a pas de compte sur l'application !!!";
        }
    }
}
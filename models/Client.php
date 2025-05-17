<?php 
    class Client{
        private $pdo;
        
        public function __construct($pdo)
        {
            $this->pdo = $pdo;
        }

        public function getUser($telephone) {
            $sql = "SELECT * FROM clients WHERE telephone = :telephone";
            $query = $this->pdo->prepare($sql);
            $query->execute([
                'telephone' => $telephone
            ]);
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        public function getOperations($telephone) {
            $operations = [];
            $sqlQuery1 = "SELECT u.prenom AS prenom_commercial, u.nom AS nom_commercial, montant, date_operation, type_operation FROM clients AS c 
                JOIN operations AS o ON c.telephone = o.telephone_client
                JOIN utilisateurs AS u ON o.id_commercial = u.id
                WHERE telephone_client = ?
                AND type_operation = 'depot' 
                AND validation_operation = ?
            ";
            $query1 = $this->pdo->prepare($sqlQuery1);
            $query1->execute([
                $telephone,
                true
            ]);
            $depots = $query1->fetchAll(PDO::FETCH_ASSOC);
            array_push($operations, $depots);
            // retrait
            $sqlQuery2 = "SELECT u.prenom AS prenom_commercial, u.nom AS nom_commercial, montant, date_operation, type_operation FROM clients AS c 
                JOIN operations AS o ON c.telephone = o.telephone_client
                JOIN utilisateurs AS u ON o.id_commercial = u.id
                WHERE telephone_client = ?
                AND type_operation = 'retrait' 
                AND validation_operation = ?
            ";
            $query2 = $this->pdo->prepare($sqlQuery2);
            $query2->execute([
                $telephone,
                true
            ]);
            $retraits = $query2->fetchAll(PDO::FETCH_ASSOC);
            array_push($operations, $retraits);

            // transfert_entrant
            $sqlQuery3 = "SELECT telephone_client AS telephone_expediteur, c.prenom AS prenom_expediteur, c.nom AS nom_expediteur, montant, date_operation , type_operation
                FROM clients AS c 
                JOIN operations AS o ON c.telephone = o.telephone_client
                JOIN clients AS c2 ON o.telephone_destinataire = c2.telephone
                WHERE telephone_destinataire = ?
                AND type_operation = 'transfert_entrant' 
                AND validation_operation = ?
            ";
            $query3 = $this->pdo->prepare($sqlQuery3);
            $query3->execute([
                $telephone,
                true
            ]);
            
            $entrants = $query3->fetchAll(PDO::FETCH_ASSOC);
            array_push($operations, $entrants);
            
            // transfert_sortant
            $sqlQuery4 = "SELECT telephone_destinataire AS telephone_destinataire, c2.prenom AS prenom_destinataire, c2.nom AS nom_destinataire, montant, date_operation, type_operation
                FROM clients AS c 
                JOIN operations AS o ON c.telephone = o.telephone_client
                JOIN clients AS c2 ON o.telephone_destinataire = c2.telephone
                WHERE telephone_client = ?
                AND type_operation = 'transfert_sortant' 
                AND validation_operation = ?
            ";
            $query4 = $this->pdo->prepare($sqlQuery4);
            $query4->execute([
                $telephone,
                true
            ]);
            
            $sortants = $query4->fetchAll(PDO::FETCH_ASSOC);
            array_push($operations, $sortants);
            
            return $operations;
        }
        
        public function createUser() {
            if(isset($_FILES["document"]) && $_FILES["document"]["error"] == 0) {
                // On verifie si le numero ou l'email existe déjà
                $sqlQuery = 'SELECT * FROM clients WHERE telephone= :telephone';
                $query = $this->pdo->prepare($sqlQuery);
                $query->execute(['telephone' => $_POST["telephone"]]);
                $telephone_verif = $query->fetch();
                
                $sqlQuery = 'SELECT * FROM clients WHERE email= :email';
                $query = $this->pdo->prepare($sqlQuery);
                $query->execute(['email' => $_POST["email"]]);
                $email_verif = $query->fetch();
                if (!empty($telephone_verif)) {
                    return 'Ce numéro de téléphone est déjà enregistré !!!';
                } elseif(!empty($email_verif)) {
                    return 'Cet email est déjà enregistré !!!';
                }
                
                
                // Création d'un nouveau nom de fichier conforme au client
                $extension = explode('.',$_FILES["document"]["name"])[1];
                $filenamefinal = trim($_POST["prenom"]).'_'.$_POST["nom"].'_'.$_POST["telephone"].'.'.$extension;
                $type_document = $_POST['type_document'];
                
                // Verification du fichier
                $fichier_valide = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
                $filename = $_FILES["document"]["name"];
                $filesize = $_FILES["document"]["size"];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if(!array_key_exists($ext, $fichier_valide)) {
                    return 'Veuillez sélectionner un format de fichier valide (jpg, jpeg, png)';
                };
                $maxsize = 2 * 1024 * 1024;
                if ($filesize > $maxsize) {
                    return 'La taille du fichier est supérieure à la limite autorisée (2Mo maximum).';
                }
                
                // Création de l'utilisateur
                $telephone = $_POST["telephone"];
                $prenom = $_POST["prenom"];
                $nom = $_POST["nom"];
                $email = $_POST["email"];
                $adresse = $_POST["adresse"];
                $date_naissance = $_POST["date_naissance"];
                $mot_de_passe = $_POST["mot_de_passe"];

                $query = "INSERT INTO clients (telephone, prenom, nom, email, adresse, date_naissance, mot_de_passe) VALUES (:telephone, :prenom, :nom, :email, :adresse, :date_naissance, SHA2(:mot_de_passe, 256))";
                $insertion = $this->pdo->prepare($query);
                $insertion->execute([
                    'telephone' => $telephone,
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email,
                    'adresse' => $adresse,
                    'date_naissance' => $date_naissance,
                    'mot_de_passe' => $mot_de_passe,
                ]);

                // Création du document
                $query = "INSERT INTO documents(telephone_client, type_document, chemin_fichier) VALUES (:telephone_client, :type_document, :chemin_fichier)";
                $insertion = $this->pdo->prepare($query);
                $insertion->execute([
                    'telephone_client' => $telephone,
                    'type_document' => $type_document,
                    'chemin_fichier' => $filenamefinal,
                ]);

                // Enregistrement dans le dossier upload
                move_uploaded_file($_FILES["document"]["tmp_name"], "../../uploads/" . $filenamefinal);
                
                return 'Votre demande à été soumise et est en attende de validation.';
            }
        }
    }
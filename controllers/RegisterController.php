<?php
session_start();
require __DIR__ . '/../config/Database.php';

function registerClient() {
    $db = Database::getInstance()->getConnection(); 
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["document"]) && $_FILES["document"]["error"] == 0) {
        // On verifie si le numero ou l'email existe déjà
        $sqlQuery = 'SELECT * FROM clients WHERE telephone= :telephone';
        $query = $db->prepare($sqlQuery);
        $query->execute(['telephone' => $_POST["telephone"]]);
        $telephone_verif = $query->fetch();
        
        $sqlQuery = 'SELECT * FROM clients WHERE email= :email';
        $query = $db->prepare($sqlQuery);
        $query->execute(['email' => $_POST["email"]]);
        $email_verif = $query->fetch();
        if (!empty($telephone_verif)) {
            $_SESSION['message'] = 'Ce numéro de téléphone est déjà enregistré !!!';
            header('Location: ../views/register_client.php');
            exit();
        } elseif(!empty($email_verif)) {
            $_SESSION['message'] = 'Cet email est déjà enregistré !!!';
            header('Location: ../views/register_client.php');
            exit();
        }
        
        
        // Création d'un nouveau nom de fichier conforme au client
        $extension = explode('.',$_FILES["document"]["name"])[1];
        $filenamefinal = trim($_POST["prenom"]).'_'.$_POST["nom"].'_'.$_POST["telephone"].'.'.$extension;
        $type_document = $_POST['type_document'];
        
        // Verification du fichier
        $fichier_valide = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png", "application/pdf" => "pdf");
        $filename = $_FILES["document"]["name"];
        $filesize = $_FILES["document"]["size"];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $fichier_valide)) {
            $_SESSION['message'] = 'Veuillez sélectionner un format de fichier valide (jpg, jpeg, png, pdf)';
            header('Location: ../views/register_client.php');
            exit();
        };
        $maxsize = 2 * 1024 * 1024;
        if ($filesize > $maxsize) {
            $_SESSION['message'] = 'La taille du fichier est supérieure à la limite autorisée (2Mo maximum).';
            header('Location: ../views/register_client.php');
            exit();
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
        $insertion = $db->prepare($query);
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
        $insertion = $db->prepare($query);
        $insertion->execute([
            'telephone_client' => $telephone,
            'type_document' => $type_document,
            'chemin_fichier' => $filenamefinal,
        ]);

        // Enregistrement dans le dossier upload
        move_uploaded_file($_FILES["document"]["tmp_name"], "../uploads/" . $filenamefinal);
        
        $_SESSION['message'] = 'Votre demande à été soumise et est en attende de validation.'.
        
        header('Location: ../views/login.php');
        exit();
    }
}

function registerUser() {
    $db = Database::getInstance()->getConnection(); 
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // On verifie si le numero ou l'email existe déjà
        
        $sqlQuery = 'SELECT * FROM utilisateurs WHERE email= :email';
        $query = $db->prepare($sqlQuery);
        $query->execute(['email' => $_POST["email"]]);
        $email_verif = $query->fetch();
        if (!empty($email_verif)) {
            $_SESSION['message'] = 'Cet email est déjà enregistré !!!';
            header('Location: ../views/register_user.php');
            exit();
        }
        
        // Création de l'utilisateur
        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $email = $_POST["email"];
        $mot_de_passe = $_POST["mot_de_passe"];

        $query = "INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe) VALUES (:prenom, :nom, :email, SHA2(:mot_de_passe, 256))";
        $insertion = $db->prepare($query);
        $insertion->execute([
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe,
        ]);
        
        $_SESSION['message'] = "Votre demande à été soumise et est en attende de validation par l'administrateur.".
        
        header('Location: ../views/login.php');
        exit();
    }
}
switch ($_POST["action"]) {
    case 'Register Client':
        registerClient();
        break;
    case 'Register User':
        registerUser();
        break;
}
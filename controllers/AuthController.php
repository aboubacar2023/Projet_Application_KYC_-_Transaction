<?php
require __DIR__ . '/../config/Database.php';

class Auth
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function login($email, $password, $type)
    {
        $mot_de_passe = hash('sha256', $password);
        $query = "SELECT * FROM $type WHERE email = :email AND mot_de_passe = :mot_de_passe";
        $recuperation = $this->db->prepare($query);
        $recuperation->execute([
            'email'=>  $email, 
            'mot_de_passe' => $mot_de_passe
        ]);
        $data = $recuperation->fetch();
        // On verifie si l'utilisateur est active
        switch ($type) {
            case 'clients':
                if (isset($data['statut']) && $data['statut'] === 'verifie_total') {
                    $_SESSION['user'] = $data;
                    return true;
                } else {
                    $_SESSION['error'] = "Votre demande d'incription est toujours en traitement !!!";
                }
                
                break;
            
            default:
                if (isset($data['validation_admin']) && $data['validation_admin'] === 1) {
                    $_SESSION['user'] = $data;
                    return true;
                } else {
                    $_SESSION['error'] = "Cet utilisateur n'est pas encore active !!!";
                }
                break;
        }
        return false;
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function logout()
    {
        session_destroy();
        header('Location: /Projet_Application_KYC_&_Transaction/views/login.php');
        exit;
    }
    public function getUser()
    {
        return $_SESSION['user'] ?? null;
    }
}
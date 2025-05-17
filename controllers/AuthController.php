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
                } elseif(empty($data)) {
                    $_SESSION['error'] = "Email ou mot de passe incorrecte !!!";
                } else {
                    if ($data['statut'] === 'rejeté') {
                        $_SESSION['error'] = "Votre demande a été rejetée !!!";
                    } else {
                        $_SESSION['error'] = "Votre demande d'incription est toujours en traitement !!!";
                    }
                }
                
                break;
            
            default:
                if (isset($data['validation_admin']) && $data['validation_admin'] === 1) {
                    $_SESSION['user'] = $data;
                    return true;
                } elseif(empty($data)) {
                    $_SESSION['error'] = "Email ou mot de passe incorrecte !!!";
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
    // Verification pour les acces aux routes
    public function verificationNiveau() {
        // On regarde si c'est un client ou un utilisateur
        if (isset($_SESSION['user']['role'])) {
            $uri = $_SERVER['REQUEST_URI']; 
            $afterViews = explode('/views/', $uri)[1];
            $route_base = explode('/', $afterViews)[0];
            $role = preg_replace('/[0-9]/', '', $_SESSION['user']['role']);
            if (str_contains($route_base, $role)) {
                return true;
            } else {
                return false;
            }
        } else {
            $uri = $_SERVER['REQUEST_URI']; 
            $afterViews = explode('/views/', $uri)[1];
            $route_base = explode('/', $afterViews)[0];
            if (isset($_SESSION['user']['telephone']) && str_contains($route_base, 'client')) {
                return true;
            } else {
                return false;
            }
        }
        
        
    }

    public function logout()
    {
        session_destroy();
        header('Location: /views/login.php');
        exit();
    }
    public function getUser()
    {
        return $_SESSION['user'] ?? null;
    }
}
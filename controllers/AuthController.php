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
        $password = hash('sha256', $password);
        $query = "SELECT * FROM $type WHERE email = :email AND password = :password";
        $recuperation = $this->db->prepare($query);
        $recuperation->execute([
            'email'=>  $email, 
            'password' => $password
        ]);
        $data = $recuperation->fetch();
        if ($data && $data['etat'] === 1) {
            $_SESSION['user'] = $data;
            return true;
        }
        if ($data['etat'] === 0) {
            $_SESSION['error'] = "Cet utilisateur n'est pas active !!!";
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
<?php
session_start();
require __DIR__ . '/../config/Database.php';

$db = Database::getInstance()->getConnection();
$id = $_POST['id'];
$role = $_POST['role'];
$validation_admin = true;
$sqlQuery = 'UPDATE utilisateurs SET role = :role, validation_admin = :validation_admin WHERE id = :id';
$query = $db->prepare($sqlQuery);
$query->execute([
    'role' => $role,
    'id' => $id,
    'validation_admin' => $validation_admin
]);
header('Location: ../views/admin/utilisateurs.php');
exit();
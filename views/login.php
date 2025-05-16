<?php
require 'racine.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->login($_POST['email'], $_POST['password'],$_POST['type'] )) {
        if ($_POST['type'] === 'clients') {
            header('Location: client/profil.php');
            exit();
        } else {
            switch ($_SESSION['user']['role']) {
                
                case 'commercial':
                    header('Location: commercial/depot.php');
                    exit;
                    break;
                case 'admin':
                    header('Location: admin/utilisateurs.php');
                    exit;
                    break;
                default:
                    header('Location: agent/client_liste.php');
                    exit;
                    break;
            }
        }
        
    } else {
        if (isset($_SESSION['error'])) {
            $error = $_SESSION['error'];
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
        
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>Page Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen">
    <form method="POST" class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-2/5">
        <h1 class="text-2xl mb-6 text-gray-900 dark:text-white">Connexion</h1>
        <?php if (isset($error)) echo "<p class='text-red-500 pb-3'>$error</p>"; ?>
        <?php if (isset($_SESSION['message'])) {
            echo '<p class="text-green-500 pb-3">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']); // Supprime le message après l'affichage
        } ?>
        <input type="email" name="email" placeholder="Votre email" required class="w-full p-2 mb-4 border rounded">
        <input type="password" name="password" placeholder="Votre mot de passe" required
            class="w-full p-2 mb-4 border rounded">
        <select type="text" name="type" required
            class="text-black block w-full p-2 mb-4 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <option value="" disabled selected hidden>Sélectionnez le Type</option>
            <option value="clients" class="text-black">Client</option>
            <option value="utilisateurs" class="text-black">Utilisateur</option>
        </select>
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white p-2 rounded">Se connecter</button>
        <p class="mt-4 text-gray-700 dark:text-gray-300"><a href="register_client.php" class="text-blue-500">Créer
                un compte Client</a></p>
        <p class="mt-1 text-gray-700 dark:text-gray-300"><a href="register_user.php" class="text-blue-500">Créer
                un compte Utilisateur</a></p>
    </form>
</body>

</html>
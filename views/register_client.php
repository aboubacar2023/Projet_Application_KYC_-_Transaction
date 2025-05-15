<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen w-auto">
    <form action="../controllers/RegisterController.php" method="POST"
        class="bg-white dark:bg-gray-800 p-8 rounded shadow-md w-3/4" enctype="multipart/form-data">
        <h1 class="text-2xl mb-6 text-gray-900 dark:text-white">Enregistrement</h1>
        <?php if (isset($_SESSION['message'])) {
            echo '<p class="text-red-500 pb-3">' . htmlspecialchars($_SESSION['message']) . '</p>';
            unset($_SESSION['message']); // Supprime le message après l'affichage
        } ?>
        <div class="grid grid-cols-3 gap-3">
            <input type="text" name="action" value="Register Client" hidden>
            <input type="text" name="telephone" placeholder="Votre Contact" required
                class="w-full p-2 mb-4 border rounded" minlength="8" maxlength="8">
            <input type="text" name="prenom" placeholder="Votre Prénom" required class="w-full p-2 mb-4 border rounded">
            <input type="text" name="nom" placeholder="Votre Nom" required class="w-full p-2 mb-4 border rounded">
        </div>
        <div class="grid grid-cols-2 gap-3">
            <input type="email" name="email" placeholder=" Votre email" required class="w-full p-2 mb-4 border rounded">
            <input type="text" name="adresse" placeholder=" Votre Adresse" required
                class="w-full p-2 mb-4 border rounded">
            <input type="date" name="date_naissance" placeholder=" Votre Date de Naissance" required
                class="w-full p-2 mb-4 border rounded">
            <input type="password" name="mot_de_passe" placeholder="Votre mot de passe" required
                class="w-full p-2 mb-4 border rounded">
            <select type="text" name="type_document" required class="text-black block w-full p-2 mb-4 border rounded">
                <option value="" disabled selected hidden>Sélectionnez le Type de document</option>
                <option value="Carte Nina" class="text-black">Carte Nina</option>
                <option value="Carte d'identité" class="text-black">Carte d'identité</option>
                <option value="Fiche Individuelle" class="text-black">Fiche Individuelle</option>
                <option value="Passeport" class=" text-black">Passeport</option>
            </select>
            <input type="file" name="document" placeholder="" accept=".jpg,.jpeg,.png,.pdf" required
                class="text-black w-full p-2 mb-4 border rounded">
        </div>
        <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white p-2 rounded">Enregistrer</button>
        <p class="mt-4 text-gray-700 dark:text-gray-300"><a href="login.php" class="text-blue-500">Se
                connecter</a></p>
    </form>
</body>

</html>
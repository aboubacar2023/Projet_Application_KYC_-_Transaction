<?php
ob_start(); 
session_start();
require __DIR__ . '/../../controllers/AuthController.php';
$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}
?>
<div class="content-area overflow-y-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Les opérations de retrait d'argents</h1>
    </div>

    <div class="overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <form action="../../controllers/CommercialController.php" method="POST">
                <?php if (isset($_SESSION['erreur_retrait'])) {
                    echo '<p class="text-red-500 pb-3">' . htmlspecialchars($_SESSION['erreur_retrait']) . '</p>';
                    unset($_SESSION['erreur_retrait']); 
                } ?>
                <?php if (isset($_SESSION['success_retrait'])) {
                    echo '<p class="text-green-500 pb-3">' . htmlspecialchars($_SESSION['success_retrait']) . '</p>';
                    unset($_SESSION['success_retrait']); 
                } ?>
                <div class="grid grid-cols-2 gap-y-3 gap-x-8">
                    <input type="text" hidden name="action" value="retrait">
                    <div>
                        <label class="text-sm font-medium text-gray-100" name='telephone'>Numéro de télephone</label>
                        <input type="text" placeholder="70707070" required minlength="8" maxlength="8"
                            class="text-black w-full mt-3 p-2 mb-3 border rounded font-bold" name='telephone'>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-100" name='montant'>Montant à rétirer
                            (FCFA)</label>
                        <input type="text" placeholder="5000" required
                            class="text-black w-full mt-3 p-2 mb-3 border rounded font-bold" name='montant'>
                    </div>
                </div>
                <div class="flex justify-center pt-3">
                    <button type="submit"
                        class="w-1/4 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded ">Envoyer</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'base_commercial.php';
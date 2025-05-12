<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->logout();
}

$url = $_SERVER["REQUEST_URI"];
// $user = $auth->getUser();
?>
<!DOCTYPE html>
<html lang="fr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    dark: {
                        100: '#1E1E2D',
                        200: '#2D2D3A',
                        300: '#3A3A48',
                        400: '#4A4A5A',
                    },
                    primary: {
                        light: '#6366f1',
                        DEFAULT: '#4f46e5',
                        dark: '#4338ca',
                    }
                }
            }
        }
    }
    </script>
    <style>
    .sidebar-item.active {
        background-color: rgba(79, 70, 229, 0.1);
        border-left: 4px solid #4f46e5;
    }

    .sidebar-item.active .sidebar-icon {
        color: #4f46e5;
    }

    .sidebar-item:hover:not(.active) {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .content-area {
        height: calc(100vh - 65px);
    }

    .article-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>

<body class="bg-gray-100 dark:bg-dark-100 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-dark-200 border-r border-dark-300">
                <div class="flex items-center justify-center h-16 px-4 border-b border-dark-300">
                    <div class="flex items-center">
                        <span class="text-xl font-bold">KYC & Transaction APP</span>
                    </div>
                </div>

                <!-- Menu -->
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="space-y-1">
                        <a href="/Projet_Application_KYC_&_Transaction/views/client/profil.php"
                            class="sidebar-item <?= str_contains($url, 'profil') === true ? 'active' : ''  ?>  flex items-center px-4 py-3 rounded-lg">
                            <i class="sidebar-icon fas fa-person mr-3 text-lg"></i>
                            <span>Page de Profil</span>
                        </a>
                        <a href="/Projet_Application_KYC_&_Transaction/views/client/transferts.php"
                            class="sidebar-item <?= str_contains($url, 'transferts') === true ? 'active' : ''  ?> flex items-center px-4 py-3 rounded-lg">
                            <i class="sidebar-icon fas fa-money-bill-transfer mr-3 text-lg"></i>
                            <span>Transfert d’argent</span>
                        </a>
                        <a href="/Projet_Application_KYC_&_Transaction/views/client/historiques.php"
                            class="sidebar-item <?= str_contains($url, 'historiques') === true ? 'active' : ''  ?> flex items-center px-4 py-3 rounded-lg">
                            <i class="sidebar-icon fas fa-file-alt mr-3 text-lg"></i>
                            <span>Historique des opérations</span>
                        </a>
                    </nav>

                    <div class="mt-auto mb-4 pt-4 border-t border-dark-300">
                        <form method="POST">
                            <button type="submit">
                                <div class="px-4 py-3 rounded-lg hover:bg-dark-300 cursor-pointer">
                                    <i class="sidebar-icon fa-solid fa-right-from-bracket mr-3 text-lg"></i>
                                    <span>Se Déconnecter</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class=" flex flex-col flex-1 overflow-hidden">
            <!-- Top bar -->

            <div class="flex items-center justify-between h-16 px-6 border-b border-dark-300 bg-dark-200">
                <!-- <h2 class="text-xl font-bold"><?= $user["prenom"] ?> <?= $user["nom"] ?></h2> -->
                <div class=" flex items-center">
                    <!-- Mobile menu button -->
                    <button class="md:hidden mr-4 text-gray-400 hover:text-white focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <?= $content ?>
        </div>
    </div>

    <script>
    // Toggle dark mode
    document.getElementById('theme-toggle').addEventListener('click', function() {
        document.documentElement.classList.toggle('dark');
        const icon = this.querySelector('i');
        if (document.documentElement.classList.contains('dark')) {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
        } else {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    });

    // Mobile menu toggle (would need implementation)
    document.querySelector('.md\\:hidden button').addEventListener('click', function() {
        // Implementation for mobile menu toggle would go here
        console.log('Mobile menu clicked');
    });

    // Active menu item
    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove(
                'active'));
            this.classList.add('active');
        });
    });
    </script>
</body>

</html>
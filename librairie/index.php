<?php
// Démarrer la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure les fichiers nécessaires
require_once('config.php');

// Définir les routes disponibles
$routes = [
    'home' => 'home.php',
    'login' => 'login.php',
    'logout' => 'logout.php',
    'profile' => 'profile.php',
    'register' => 'register.php',
    'edit_profile' => 'edit_profile.php',
    'books' => 'books.php',
    'add_book' => 'add_book.php',
    'edit_book' => 'edit_book.php',
    'delete_book' => 'delete_book.php',
    'book_details' => 'book_details.php',
];

// Vérifier si l'utilisateur est connecté, sauf pour certaines pages
$publicRoutes = ['login', 'register', 'book_details'];
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Vérification de l'accès utilisateur
if (!in_array($page, $publicRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Vérifier si la page demandée existe
if (array_key_exists($page, $routes)) {
    ob_start(); // Capture le contenu de la page
    include($routes[$page]);
    $content = ob_get_clean(); // Stocker le contenu capturé
} else {
    // Page inexistante : afficher une erreur 404
    http_response_code(404);
    $content = "<h1>Erreur 404 : Page non trouvée</h1><p>La page demandée n'existe pas.</p>";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librairie XYZ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>Librairie XYZ</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav>
            <a href="index.php?page=home">Accueil</a>
            <a href="index.php?page=books">Livres</a>
            <a href="index.php?page=profile">Profil</a>
            <a href="index.php?page=logout">Déconnexion</a>
        </nav>
    <?php else: ?>
        <nav>
            <a href="index.php?page=login">Connexion</a>
            <a href="index.php?page=register">Inscription</a>
        </nav>
    <?php endif; ?>
</header>

<main>
    <?= $content ?>
</main>

<footer>
    <p>&copy; <?= date("Y"); ?> Librairie XYZ</p>
</footer>
</body>
</html>

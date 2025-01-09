<?php
// Démarrer la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure la configuration
require_once('config.php');

// Extraire la route depuis l'URL
$requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

// Définir les routes disponibles
// Définir les routes disponibles
$routes = [
    '/' => 'pages/home.php',
    '/login' => 'pages/login.php',
    '/logout' => 'pages/logout.php',
    '/profile' => 'pages/profile.php',
    '/register' => 'pages/register.php',
    '/edit-profile' => 'pages/edit_profile.php',
    '/books' => 'pages/books.php',
    '/add-book' => 'pages/add_book.php',
    '/edit-book' => 'pages/edit_book.php',
    '/delete-book' => 'pages/delete_book.php',
    '/book-details' => 'pages/book_details.php',
    '/borrow-dashboard' => 'pages/borrow_dashboard.php',
    '/my-borrows' => 'pages/my_borrows.php', 
    '/borrow' => 'pages/borrow_book.php',
    '/return-book' => 'pages/return_book.php', 
];

// Définir les routes publiques (accessibles sans connexion)
$publicRoutes = ['/login', '/register', '/book-details'];

// Vérifier l'accès utilisateur
if (!in_array($requestUri, $publicRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Vérifier si la route existe
if (array_key_exists($requestUri, $routes)) {
    ob_start(); // Capture le contenu de la page
    include($routes[$requestUri]);
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
    <!-- Inclusion de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <!-- Barre de navigation Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Librairie XYZ</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/books">Livres</a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="/add-book">Ajouter un Livre</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($_SESSION['role'] === 'utilisateur'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="/borrow-dashboard">Mes Emprunts</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/profile">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/logout">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/login">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/register">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<footer class="bg-primary text-white text-center py-3">
    <p>&copy; <?= date("Y"); ?> Librairie XYZ</p>
</footer>

<!-- Inclusion des scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



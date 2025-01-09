<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est authentifié
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'utilisateur') {
    header('Location: /login');
    exit();
}

// Vérifiez les emprunts en retard
$user_id = $_SESSION['user_id'];
$queryOverdue = "
    SELECT livres.titre, emprunts.date_retour 
    FROM emprunts 
    INNER JOIN livres ON emprunts.book_id = livres.id
    WHERE emprunts.user_id = :user_id 
    AND emprunts.date_retour < CURDATE() 
    AND emprunts.statut = 'emprunté'
";
$stmtOverdue = $pdo->prepare($queryOverdue);
$stmtOverdue->execute([':user_id' => $user_id]);
$overdueBooks = $stmtOverdue->fetchAll(PDO::FETCH_ASSOC);

// Afficher une alerte si des emprunts sont en retard
if (!empty($overdueBooks)) {
    $_SESSION['overdue_alert'] = "Vous avez des emprunts en retard ! Veuillez retourner ces livres dès que possible.";
}

// Affichage des alertes générales (succès ou erreurs)
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success text-center">' . htmlspecialchars($_SESSION['success']) . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger text-center">' . htmlspecialchars($_SESSION['error']) . '</div>';
    unset($_SESSION['error']);
}

// Affichage de l'alerte pour les emprunts en retard
if (isset($_SESSION['overdue_alert'])) {
    echo '<div class="alert alert-warning text-center">' . htmlspecialchars($_SESSION['overdue_alert']) . '</div>';
    unset($_SESSION['overdue_alert']);
}
?>


<h1>Gestion des emprunts</h1>

<div class="container mt-5">

    <h2 class="text-center">Bienvenue dans votre espace emprunts</h2>

    <div class="text-center mt-4">
        <a href="/borrow" class="btn btn-primary">Emprunter un livre</a>
        <a href="/my-borrows" class="btn btn-secondary">Voir mes emprunts</a>
        <a href="/" class="btn btn-danger">Retour à l'accueil</a>
    </div>
</div>

<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification des paramètres
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Aucun livre spécifié pour la suppression.";
    header('Location: /books');
    exit();
}

$book_id = (int) $_GET['id'];

// Vérification des permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Vous n'avez pas les autorisations nécessaires pour supprimer un livre.";
    header('Location: /login');
    exit();
}

// Supprimer le livre
try {
    $deleteQuery = "DELETE FROM livres WHERE id = :id";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([':id' => $book_id]);

    if ($deleteStmt->rowCount() > 0) {
        $_SESSION['success'] = "Le livre a été supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Le livre spécifié n'existe pas ou a déjà été supprimé.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Une erreur est survenue lors de la suppression : " . $e->getMessage();
}

// Redirection vers la liste des livres
header('Location: /books');
exit();
?>

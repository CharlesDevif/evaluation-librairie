<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est authentifié
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'utilisateur') {
    header('Location: /login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier si un ID de livre est passé
if (!isset($_GET['book_id'])) {
    $_SESSION['error'] = "Aucun livre spécifié pour le retour.";
    header('Location: /borrow-dashboard');
    exit();
}

$book_id = $_GET['book_id'];

// Vérifier que l'utilisateur a emprunté ce livre
$query = "SELECT * FROM emprunts WHERE user_id = :user_id AND book_id = :book_id AND statut = 'emprunté'";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':user_id' => $user_id,
    ':book_id' => $book_id
]);
$borrow = $stmt->fetch();

if (!$borrow) {
    $_SESSION['error'] = "Vous n'avez pas emprunté ce livre ou il a déjà été retourné.";
    header('Location: /borrow-dashboard');
    exit();
}

// Marquer le livre comme retourné
$updateQuery = "UPDATE emprunts SET statut = 'retourné' WHERE id = :id";
$updateStmt = $pdo->prepare($updateQuery);
$updateStmt->execute([':id' => $borrow['id']]);

// Mettre à jour le statut du livre
$updateBookQuery = "UPDATE livres SET statut = 'disponible' WHERE id = :book_id";
$updateBookStmt = $pdo->prepare($updateBookQuery);
$updateBookStmt->execute([':book_id' => $book_id]);

// Définir un message de succès
$_SESSION['success'] = "Le livre a été retourné avec succès.";
header('Location: /borrow-dashboard');
exit();
?>

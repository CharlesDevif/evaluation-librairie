<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$errors = [];
$success = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = $_POST['book_id'] ?? null;
    $returnDate = $_POST['return_date'] ?? null;
    $userId = $_SESSION['user_id'];

    // Validation des données
    if (empty($bookId)) {
        $errors[] = "Veuillez sélectionner un livre.";
    }
    if (empty($returnDate)) {
        $errors[] = "Veuillez spécifier une date de retour.";
    } else {
        $today = new DateTime();
        $returnDateObj = new DateTime($returnDate);
        $diff = $today->diff($returnDateObj)->days;

        if ($diff > 30) {
            $errors[] = "La durée maximale d'emprunt est de 30 jours.";
        } elseif ($returnDateObj < $today) {
            $errors[] = "La date de retour ne peut pas être dans le passé.";
        }
    }

    // Si aucune erreur, insérer l'emprunt dans la base de données
    if (empty($errors)) {
        // Vérifier si le livre est disponible
        $query = "SELECT statut FROM livres WHERE id = :book_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':book_id' => $bookId]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['statut'] === 'disponible') {
            // Marquer le livre comme emprunté
            $updateQuery = "UPDATE livres SET statut = 'emprunter' WHERE id = :book_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([':book_id' => $bookId]);

            // Ajouter l'emprunt
            $insertQuery = "INSERT INTO emprunts (user_id, book_id, date_emprunt, date_retour) 
                            VALUES (:user_id, :book_id, NOW(), :return_date)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([
                ':user_id' => $userId,
                ':book_id' => $bookId,
                ':return_date' => $returnDate,
            ]);

            $success = true;
        } else {
            $errors[] = "Ce livre n'est pas disponible.";
        }
    }
}

// Récupérer la liste des livres disponibles
$query = "SELECT id, titre FROM livres WHERE statut = 'disponible'";
$stmt = $pdo->query($query);
$availableBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="container mt-5">
    <h1 class="text-center">Emprunter un Livre</h1>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            Votre emprunt a été enregistré avec succès !
        </div>
        <div class="text-center">
            <a href="/books" class="btn btn-primary">Retour à la liste des livres</a>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="book_id" class="form-label">Sélectionnez un livre :</label>
                <select name="book_id" id="book_id" class="form-select" required>
                    <option value="">-- Sélectionnez un livre --</option>
                    <?php foreach ($availableBooks as $book): ?>
                        <option value="<?= htmlspecialchars($book['id']) ?>"><?= htmlspecialchars($book['titre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="return_date" class="form-label">Date de retour prévue :</label>
                <input type="date" name="return_date" id="return_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Emprunter</button>
        </form>
    <?php endif; ?>
</div>



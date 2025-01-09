<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification des permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

// Vérification de l'ID du livre
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Aucun livre spécifié pour la modification.";
    header('Location: /books');
    exit();
}

$book_id = (int) $_GET['id'];

// Récupérer les détails du livre
$query = "SELECT * FROM livres WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    $_SESSION['error'] = "Livre introuvable.";
    header('Location: /books');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $title = htmlspecialchars(trim($_POST['title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $description = htmlspecialchars(trim($_POST['description']));
    $date_publication = $_POST['date_publication'];
    $isbn = htmlspecialchars(trim($_POST['isbn']));
    $coverUrl = htmlspecialchars(trim($_POST['cover_url']));

    // Validation des champs
    if (empty($title)) {
        $errors[] = "Le titre est requis.";
    }
    if (empty($author)) {
        $errors[] = "L'auteur est requis.";
    }
    if (empty($description)) {
        $errors[] = "La description est requise.";
    }
    if (empty($date_publication)) {
        $errors[] = "La date de publication est requise.";
    }
    if (empty($isbn)) {
        $errors[] = "L'ISBN est requis.";
    }
    if (empty($coverUrl) || !filter_var($coverUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "Une URL valide pour l'image est requise.";
    }

    // Vérification CAPTCHA
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Clé secrète de test reCAPTCHA
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse");
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        $errors[] = "CAPTCHA non valide. Veuillez réessayer.";
    }

    // Si aucune erreur, mettre à jour le livre
    if (empty($errors)) {
        $updateQuery = "UPDATE livres 
                        SET titre = :title, auteur = :author, description = :description, 
                            date_publication = :date_publication, isbn = :isbn, photo_url = :cover_url 
                        WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':description' => $description,
            ':date_publication' => $date_publication,
            ':isbn' => $isbn,
            ':cover_url' => $coverUrl,
            ':id' => $book_id,
        ]);

        $success = true;
    }
}
?>

<div class="container mt-5">
    <h1 class="text-center">Modifier un livre - Librairie XYZ</h1>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            Les modifications ont été enregistrées avec succès.
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
                <label for="title" class="form-label">Titre :</label>
                <input type="text" name="title" value="<?= htmlspecialchars($book['titre']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Auteur :</label>
                <input type="text" name="author" value="<?= htmlspecialchars($book['auteur']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($book['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="date_publication" class="form-label">Date de publication :</label>
                <input type="date" name="date_publication" value="<?= htmlspecialchars($book['date_publication']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN :</label>
                <input type="text" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="cover_url" class="form-label">URL de l'image :</label>
                <input type="text" name="cover_url" value="<?= htmlspecialchars($book['photo_url']) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div> <!-- Clé de test publique -->
            </div>
            <button type="submit" class="btn btn-success">Enregistrer les Modifications</button>
        </form>
        <div class="text-center mt-4">
            <a href="/books" class="btn btn-secondary">Retour à la liste des livres</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

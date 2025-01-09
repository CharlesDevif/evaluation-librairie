<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification du rôle de l'utilisateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $title = htmlspecialchars(trim($_POST['title']));
    $author = htmlspecialchars(trim($_POST['author']));
    $description = htmlspecialchars(trim($_POST['description']));
    $date_publication = $_POST['date_publication'];
    $isbn = htmlspecialchars(trim($_POST['isbn']));
    $coverPath = htmlspecialchars(trim($_POST['cover']));

    // Validation des champs
    if (empty($title)) {
        $errors[] = "Le titre du livre est requis.";
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
    if (empty($coverPath) || !filter_var($coverPath, FILTER_VALIDATE_URL)) {
        $errors[] = "Une URL valide pour l'image est requise.";
    }

    // Vérification CAPTCHA (mode test)
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'; // Clé secrète de test Google reCAPTCHA
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse");
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        $errors[] = "CAPTCHA non valide. Veuillez réessayer.";
    }

    // Si aucune erreur, insérer dans la base de données
    if (empty($errors)) {
        $query = "INSERT INTO livres (titre, auteur, description, date_publication, isbn, photo_url) 
                  VALUES (:title, :author, :description, :date_publication, :isbn, :photo_url)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':description' => $description,
            ':date_publication' => $date_publication,
            ':isbn' => $isbn,
            ':photo_url' => $coverPath,
        ]);

        $success = true;
    }
}
?>

<div class="container mt-5">
    <h1 class="text-center">Ajouter un livre - Librairie XYZ</h1>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            Le livre a été ajouté avec succès.
        </div>
        <div class="text-center">
            <a href="/books" class="btn btn-primary">Retour à la gestion des livres</a>
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
                <label for="cover" class="form-label">URL de l'image :</label>
                <input type="text" name="cover" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Titre :</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Auteur :</label>
                <input type="text" name="author" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description :</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="date_publication" class="form-label">Date de publication :</label>
                <input type="date" name="date_publication" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="isbn" class="form-label">ISBN :</label>
                <input type="text" name="isbn" class="form-control" required>
            </div>
            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div> <!-- Clé de test publique -->
            </div>
            <button type="submit" class="btn btn-success">Ajouter le Livre</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

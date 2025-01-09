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

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté
$errors = [];
$success = false;

// Pré-remplir les champs avec des valeurs existantes
$currentName = isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : '';
$currentEmail = isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $newName = htmlspecialchars(trim($_POST['new_name'] ?? ''));
    $newEmail = filter_var($_POST['new_email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validation des champs
    if (empty($newName)) {
        $errors[] = "Le nom est requis.";
    }
    if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }

    // Vérifier si l'email existe déjà dans la base de données
    if (empty($errors)) {
        $query = "SELECT id FROM utilisateurs WHERE email = :email AND id != :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':email' => $newEmail,
            ':user_id' => $user_id,
        ]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Cet email est déjà utilisé par un autre utilisateur.";
        }
    }

    // Mise à jour des informations
    if (empty($errors)) {
        $updateQuery = "UPDATE utilisateurs SET nom = :new_name, email = :new_email WHERE id = :user_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([
            ':new_name' => $newName,
            ':new_email' => $newEmail,
            ':user_id' => $user_id,
        ]);

        $success = true;
        $_SESSION['success'] = "Votre profil a été mis à jour avec succès.";
        $_SESSION['prenom'] = $newName;
        $_SESSION['user'] = $newEmail;
        header('Location: /profile');
        exit();
    }
}
?>

<div class="container mt-5">
    <h1 class="text-center">Modifier votre profil</h1>

    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            Votre profil a été mis à jour avec succès.
        </div>
        <div class="text-center">
            <a href="/profile" class="btn btn-primary">Retour au profil</a>
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
                <label for="new_name" class="form-label">Nouveau Nom :</label>
                <input type="text" name="new_name" class="form-control" value="<?= $currentName ?>" required>
            </div>
            <div class="mb-3">
                <label for="new_email" class="form-label">Nouvel Email :</label>
                <input type="email" name="new_email" class="form-control" value="<?= $currentEmail ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Enregistrer les Modifications</button>
        </form>
        <div class="text-center mt-4">
            <a href="/profile" class="btn btn-secondary">Retour au Profil</a>
            <a href="/" class="btn btn-secondary">Retour à l'Accueil</a>
        </div>
    <?php endif; ?>
</div>

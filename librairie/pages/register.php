<?php
require_once('config.php');

// Initialisation de l'erreur
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nettoyage et validation des données
    $name = htmlspecialchars(trim($_POST['name']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validation des champs
    if (empty($name) || empty($prenom) || empty($email) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } else {
        // Vérification si l'email existe déjà
        $query = "SELECT id FROM utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            // Hachage du mot de passe et insertion dans la base de données
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) 
                      VALUES (:name, :prenom, :email, :password)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':prenom' => $prenom,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);

            if ($stmt) {
                // Redirection vers la page de connexion après inscription
                header('Location: /login');
                exit;
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>


<div class="container mt-5">
    <h1 class="text-center">Inscription - Librairie XYZ</h1>

    <div class="mt-4">
        <!-- Formulaire d'inscription -->
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nom :</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nom" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" name="prenom" id="prenom" class="form-control" placeholder="Prénom" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>

        <!-- Affichage des erreurs -->
        <?php if ($error): ?>
            <div class="alert alert-danger mt-3">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Lien vers la page de connexion -->
        <p class="mt-3 text-center">
            Vous avez déjà un compte ? <a href="/login">Connectez-vous ici</a>.
        </p>
    </div>
</div>



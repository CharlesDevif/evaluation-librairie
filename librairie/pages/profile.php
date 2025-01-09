<?php
require('config.php');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Récupération des informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM utilisateurs WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userInfo) {
    echo "<p>Erreur : Utilisateur introuvable.</p>";
    exit;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informations personnelles</h4>
                </div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?= htmlspecialchars($userInfo['nom']) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($userInfo['email']) ?></p>
                    <p><strong>Prénom :</strong> <?= htmlspecialchars($userInfo['prenom']) ?></p>
                    <p><strong>Date d'inscription :</strong> <?= htmlspecialchars($userInfo['date_inscription']) ?></p>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-warning" onclick="window.location.href = '/edit-profile'">Modifier le Profil</button>
                    <button class="btn btn-secondary" onclick="window.location.href = '/'">Retour à l'accueil</button>
                </div>
            </div>
        </div>
    </div>
</div>

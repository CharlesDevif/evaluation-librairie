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

// Récupérer les emprunts de l'utilisateur
$query = "SELECT e.*, l.titre, l.auteur, l.photo_url 
          FROM emprunts e 
          JOIN livres l ON e.book_id = l.id 
          WHERE e.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$borrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h1 class="text-center">Mes emprunts</h1>

    <?php if ($borrows): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Date d'emprunt</th>
                    <th>Date de retour</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($borrows as $borrow): ?>
                    <tr>
                        <td><img src="<?= $borrow['photo_url'] ?>" alt="<?= $borrow['titre'] ?>" class="img-thumbnail" style="max-width: 100px;"></td>
                        <td><?= htmlspecialchars($borrow['titre']) ?></td>
                        <td><?= htmlspecialchars($borrow['auteur']) ?></td>
                        <td><?= htmlspecialchars($borrow['date_emprunt']) ?></td>
                        <td><?= htmlspecialchars($borrow['date_retour']) ?></td>
                        <td><?= htmlspecialchars($borrow['statut']) ?></td>
                        <td>
                            <?php if ($borrow['statut'] === 'emprunté'): ?>
                                <a href="/return-book?book_id=<?= $borrow['book_id'] ?>" class="btn btn-success btn-sm">Retourner</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning">Vous n'avez aucun emprunt en cours.</p>
    <?php endif; ?>
</div>

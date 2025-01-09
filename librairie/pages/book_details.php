<?php
require('config.php');

// Vérifier si un ID de livre est passé dans l'URL
if (isset($_GET['id'])) {
    $bookId = (int) $_GET['id'];

    // Récupération des détails du livre depuis la base de données
    $query = "SELECT * FROM livres WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':id' => $bookId]);

    if ($stmt->rowCount() === 1) {
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Livre introuvable.";
    }
} else {
    $error = "Aucun livre spécifié.";
}
?>

<div class="container mt-5">
    <h1 class="text-center">Détails du Livre</h1>

    <?php if (isset($book)): ?>
        <div class="row mt-4">
            <!-- Image du livre -->
            <div class="col-md-4">
                <img class="img-fluid rounded shadow-sm" src="<?= htmlspecialchars($book['photo_url']) ?>" alt="<?= htmlspecialchars($book['titre']) ?>">
            </div>

            <!-- Détails du livre -->
            <div class="col-md-8">
                <h3><?= htmlspecialchars($book['titre']) ?></h3>
                <p><strong>Auteur :</strong> <?= htmlspecialchars($book['auteur']) ?></p>
                <p><strong>Date de publication :</strong> <?= htmlspecialchars($book['date_publication']) ?></p>
                <p><strong>ISBN :</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                <p><strong>Description :</strong></p>
                <p><?= htmlspecialchars($book['description']) ?></p>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="mt-4 text-center">
            <a href="/books" class="btn btn-secondary">Retour à la liste des livres</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/edit-book?id=<?= $bookId ?>" class="btn btn-warning">Modifier le livre</a>
                <button class="btn btn-danger" onclick="confirmDelete(<?= $bookId ?>)">Supprimer le livre</button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
        <div class="text-center">
            <a href="/books" class="btn btn-secondary">Retour à la liste des livres</a>
        </div>
    <?php endif; ?>
</div>

<script>
    function confirmDelete(bookId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            window.location.href = "/delete-book?id=" + bookId;
        }
    }
</script>

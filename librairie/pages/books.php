<?php
require('config.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Récupération des livres depuis la base de données
$query = "SELECT * FROM livres";
$stmt = $pdo->query($query);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success text-center">
        <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>


<div class="container mt-5">
    <h1 class="text-center">Liste des Livres - Librairie XYZ</h1>

    <?php if ($books): ?>
        <table class="table table-striped table-hover mt-4">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Auteur</th>
                    <th scope="col">Date de publication</th>
                    <th scope="col">Statut</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <img class="book-image img-thumbnail" src="<?= htmlspecialchars($book['photo_url']) ?>" alt="<?= htmlspecialchars($book['titre']) ?>">
                        </td>
                        <td><?= htmlspecialchars($book['titre']) ?></td>
                        <td><?= htmlspecialchars($book['auteur']) ?></td>
                        <td><?= htmlspecialchars($book['date_publication']) ?></td>
                        <td><?= htmlspecialchars($book['statut']) ?></td>
                        <td>
                            <!-- Bouton pour voir les détails -->
                            <a href="/book-details?id=<?= $book['id'] ?>" class="btn btn-primary btn-sm">Détails</a>

                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <!-- Boutons d'action pour les admins -->
                                <a href="/edit-book?id=<?= $book['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $book['id'] ?>)">Supprimer</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning text-center">Aucun livre trouvé.</p>
    <?php endif; ?>

    <div class="text-center mt-4">
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/add-book" class="btn btn-success">Ajouter un livre</a>
        <?php endif; ?>
        <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
</div>

<script>
    function confirmDelete(bookId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            window.location.href = `/delete-book?id=${bookId}`;
        }
    }
</script>

<?php
require('config.php');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['role'];

    // Requête pour récupérer les emprunts dépassant la date de retour prévue (pour tous les utilisateurs)
    $queryOverdue = "
        SELECT livres.titre, emprunts.date_retour 
        FROM emprunts 
        INNER JOIN livres ON emprunts.book_id = livres.id
        WHERE emprunts.user_id = :user_id 
        AND emprunts.date_retour < CURDATE() 
        AND emprunts.statut = 'emprunté'
    ";
    $stmtOverdue = $pdo->prepare($queryOverdue);
    $stmtOverdue->execute([':user_id' => $userId]);
    $overdueBooks = $stmtOverdue->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour le tableau de bord administrateur uniquement
    if ($userRole === 'admin') {
        // Nombre total de livres
        $queryTotalBooks = "SELECT COUNT(*) as total_books FROM livres";
        $stmtTotalBooks = $pdo->prepare($queryTotalBooks);
        $stmtTotalBooks->execute();
        $resultTotalBooks = $stmtTotalBooks->fetch(PDO::FETCH_ASSOC);

        // Nombre d'utilisateurs enregistrés
        $queryTotalUsers = "SELECT COUNT(*) as total_users FROM utilisateurs";
        $stmtTotalUsers = $pdo->prepare($queryTotalUsers);
        $stmtTotalUsers->execute();
        $resultTotalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<div class="wrapper">

    <!-- Page Content -->
    <div id="content">
        <div class="container">
            <h1>Dashboard</h1>
            <p>Bienvenue sur la Librairie XYZ. Découvrez nos livres et gérez vos emprunts facilement.</p>

            <!-- Affichage des emprunts en retard -->
            <?php if (isset($overdueBooks) && count($overdueBooks) > 0): ?>
                <div class="alert alert-danger">
                    <h4>Attention !</h4>
                    <p>Vous avez des emprunts en retard :</p>
                    <ul>
                        <?php foreach ($overdueBooks as $book): ?>
                            <li>
                                <strong><?= htmlspecialchars($book['titre']) ?></strong> (Date de retour prévue : <?= htmlspecialchars($book['date_retour']) ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Affichage spécifique pour les administrateurs -->
            <?php if ($userRole === 'admin'): ?>
                <div class="statistic-container">
                    <div class="statistic">
                        <h3>Total des Livres</h3>
                        <p><?= htmlspecialchars($resultTotalBooks['total_books']) ?></p>
                    </div>
                    <div class="statistic">
                        <h3>Utilisateurs Enregistrés</h3>
                        <p><?= htmlspecialchars($resultTotalUsers['total_users']) ?></p>
                    </div>
                </div>
                <div class="quick-links mt-4">
                    <button onclick="window.location.href = '/books';">Gérer les Livres</button>
                    <button onclick="window.location.href = '/profile';">Mon Profil</button>
                    <button onclick="window.location.href = '/add-book';">Ajouter un Livre</button>
                </div>
            <?php endif; ?>

            <!-- Affichage spécifique pour les utilisateurs -->
            <?php if ($userRole === 'utilisateur'): ?>
                <div class="statistic-container">
                    <div class="statistic">
                        <h3>Total des Livres Disponibles</h3>
                        <p>
                            <?php 
                            // Compter les livres disponibles uniquement
                            $queryAvailableBooks = "SELECT COUNT(*) as available_books FROM livres WHERE statut = 'disponible'";
                            $stmtAvailableBooks = $pdo->prepare($queryAvailableBooks);
                            $stmtAvailableBooks->execute();
                            $resultAvailableBooks = $stmtAvailableBooks->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($resultAvailableBooks['available_books']);
                            ?>
                        </p>
                    </div>
                </div>
                <div class="quick-links mt-4">
                    <button onclick="window.location.href = '/books';">Voir les Livres</button>
                    <button onclick="window.location.href = '/borrow-book';">Emprunter un Livre</button>
                    <button onclick="window.location.href = '/profile';">Mon Profil</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

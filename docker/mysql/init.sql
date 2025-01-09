CREATE DATABASE IF NOT EXISTS library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE library;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================================
-- Structure de la table `livres`
-- ============================================================
CREATE TABLE `livres` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auteur` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_publication` DATE NOT NULL,
  `isbn` VARCHAR(13) COLLATE utf8mb4_general_ci NOT NULL,
  `description` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `statut` ENUM('disponible', 'emprunter') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'disponible',
  `photo_url` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ============================================================
-- Structure de la table `utilisateurs`
-- ============================================================
CREATE TABLE `utilisateurs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_inscription` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `role` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'utilisateur',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Données pour la table `utilisateurs`
INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `date_inscription`, `role`) VALUES
(1, 'Smith', 'John', 'john@smith.com', '$2y$10$xSEoJGdBwbJXMU3BIRD6xuLh0Be/Bz0D8FxbNbyqzHN3Ovuvfa1O2', '2023-11-09 21:54:09', 'admin'),
(2, 'Lord', 'Marc', 'marc@lord.com', '$2y$10$pY2UHGd6DcGYgmU1HgSgbOrp9g7fuFrAk1B7lIZi7anXzrFAlt5IG', '2023-11-09 21:59:23', 'utilisateur');


-- Données pour la table `livres`
INSERT INTO `livres` (`id`, `titre`, `auteur`, `date_publication`, `isbn`, `description`, `statut`, `photo_url`) VALUES
(1, 'Developpement Web mobile avec HTML, CSS et JavaScript Pour les Nuls', 'William HARREL', '2023-11-09', 'DHIDZH1374R', 
 'Un livre indispensable à tous les concepteurs ou développeurs de sites Web pour iPhone, iPad, smartphones et tablettes !', 
 'emprunté', 'https://cdn.cultura.com/cdn-cgi/image/width=180/media/pim/82_metadata-image-20983225.jpeg'),
(4, 'PHP et MySql pour les Nuls', 'Janet VALADE', '2023-11-14', '23R32R2R4', 
 'Le livre best-seller sur PHP & MySQL ! Avec cette 5e édition, vous verrez qu\'il n\'est plus nécessaire d\'être un as de la programmation.', 
 'disponible', 'https://cdn.cultura.com/cdn-cgi/image/width=830/media/pim/66_metadata-image-20983256.jpeg');


-- ============================================================
-- Configuration des AUTO_INCREMENT
-- ============================================================
ALTER TABLE `livres`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `utilisateurs`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

COMMIT;

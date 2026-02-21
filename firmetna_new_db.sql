-- ================================================================
-- FIRMETNA - Base de données complète
-- Généré depuis les entités Doctrine du projet
-- À importer dans phpMyAdmin sur la base: firmetna_new_db
-- ================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table: user
-- ----------------------------
CREATE TABLE `user` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'ROLE_USER',
  `bio` LONGTEXT DEFAULT NULL,
  `specialite` VARCHAR(100) DEFAULT NULL,
  `localisation` VARCHAR(150) DEFAULT NULL,
  `nom` VARCHAR(100) DEFAULT NULL,
  `prenom` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `adresse` VARCHAR(255) DEFAULT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `date_inscription` DATETIME DEFAULT NULL,
  `statut` VARCHAR(50) DEFAULT NULL,
  `role_type` VARCHAR(50) DEFAULT NULL,
  UNIQUE INDEX UNIQ_8D93D649E7927C74 (`email`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: lieu
-- ----------------------------
CREATE TABLE `lieu` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `adresse` VARCHAR(255) NOT NULL,
  `ville` VARCHAR(100) NOT NULL,
  `capacite` INT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `disponibilite` VARCHAR(100) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `latitude` NUMERIC(10, 8) DEFAULT NULL,
  `longitude` NUMERIC(11, 8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: event
-- ----------------------------
CREATE TABLE `event` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `date` DATETIME NOT NULL,
  `organisateur` VARCHAR(150) DEFAULT NULL,
  `lieu_id` INT DEFAULT NULL,
  INDEX IDX_3BAE0AA76AB213CC (`lieu_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_3BAE0AA76AB213CC FOREIGN KEY (`lieu_id`) REFERENCES `lieu` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: participation
-- ----------------------------
CREATE TABLE `participation` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `event_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX IDX_AB55E24F71F7E88B (`event_id`),
  INDEX IDX_AB55E24FA76ED395 (`user_id`),
  UNIQUE INDEX uniq_event_user (`event_id`, `user_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_AB55E24F71F7E88B FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: publication
-- ----------------------------
CREATE TABLE `publication` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `auteur_id` INT NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `contenu` LONGTEXT NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `date_creation` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `image_filename` VARCHAR(255) DEFAULT NULL,
  INDEX IDX_AF4C87F36BB81C92 (`auteur_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_AF4C87F36BB81C92 FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: commentaire
-- ----------------------------
CREATE TABLE `commentaire` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `auteur_id` INT NOT NULL,
  `publication_id` INT NOT NULL,
  `contenu` LONGTEXT NOT NULL,
  `date_creation` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  INDEX IDX_67F068BC6BB81C92 (`auteur_id`),
  INDEX IDX_67F068BC38B217A7 (`publication_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_67F068BC6BB81C92 FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT FK_67F068BC38B217A7 FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: produit
-- ----------------------------
CREATE TABLE `produit` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nom` VARCHAR(150) NOT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `prix` NUMERIC(10, 2) NOT NULL,
  `type` VARCHAR(20) NOT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `unite` VARCHAR(30) NOT NULL DEFAULT 'kilo',
  `stock` INT NOT NULL DEFAULT 0,
  `is_bio` TINYINT(1) NOT NULL DEFAULT 0,
  `badge` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: commande
-- ----------------------------
CREATE TABLE `commande` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `client_id` INT NOT NULL,
  `date_commande` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `statut` VARCHAR(30) NOT NULL DEFAULT 'En attente',
  `adresse_livraison` LONGTEXT DEFAULT NULL,
  `total` NUMERIC(12, 2) NOT NULL DEFAULT 0,
  `commentaire` VARCHAR(500) DEFAULT NULL,
  INDEX IDX_6EEAA67D19EB6921 (`client_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_6EEAA67D19EB6921 FOREIGN KEY (`client_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: ligne_commande
-- ----------------------------
CREATE TABLE `ligne_commande` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `commande_id` INT NOT NULL,
  `produit_id` INT NOT NULL,
  `quantite` INT NOT NULL DEFAULT 1,
  `prix_unitaire` NUMERIC(10, 2) NOT NULL,
  INDEX IDX_3170B74B82EA2E54 (`commande_id`),
  INDEX IDX_3170B74BF347EFB (`produit_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_3170B74B82EA2E54 FOREIGN KEY (`commande_id`) REFERENCES `commande` (`id`) ON DELETE CASCADE,
  CONSTRAINT FK_3170B74BF347EFB FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: offre
-- ----------------------------
CREATE TABLE `offre` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `auteur_id` INT DEFAULT NULL,
  `nom` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(25) NOT NULL,
  `categorie` VARCHAR(100) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `quantite` VARCHAR(100) DEFAULT NULL,
  `disponible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  INDEX IDX_AF86866F60BB6FE6 (`auteur_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_AF86866F60BB6FE6 FOREIGN KEY (`auteur_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: demande
-- ----------------------------
CREATE TABLE `demande` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `offre_id` INT NOT NULL,
  `demandeur_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `statut` VARCHAR(30) NOT NULL DEFAULT 'en_attente',
  INDEX IDX_2694D7A54CC8505A (`offre_id`),
  INDEX IDX_2694D7A595A6EE59 (`demandeur_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT FK_2694D7A54CC8505A FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE CASCADE,
  CONSTRAINT FK_2694D7A595A6EE59 FOREIGN KEY (`demandeur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table: messenger_messages (Symfony Messenger)
-- ----------------------------
CREATE TABLE `messenger_messages` (
  `id` BIGINT AUTO_INCREMENT NOT NULL,
  `body` LONGTEXT NOT NULL,
  `headers` LONGTEXT NOT NULL,
  `queue_name` VARCHAR(190) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `available_at` DATETIME NOT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (`queue_name`, `available_at`, `delivered_at`, `id`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Utilisateur admin par défaut
-- Mot de passe: admin123 (hashé avec bcrypt)
-- ----------------------------
INSERT INTO `user` (`email`, `password`, `role`, `nom`, `prenom`, `date_inscription`, `statut`, `role_type`)
VALUES (
  'admin@firmetna.tn',
  '$2y$13$92V2x0M8RqBfyKb8ZzVQ5.LxMGqQz8jNq3Y8p5k8lVJAL5H5K8Fkq',
  'ROLE_ADMIN',
  'Admin',
  'Firmetna',
  NOW(),
  'Actif',
  'Agriculteur'
);

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- Import terminé ! Toutes les tables ont été créées.
-- Compte admin: admin@firmetna.tn / admin123
-- ================================================================

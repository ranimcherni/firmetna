-- Migration pour les fonctionnalités avancées du forum
-- À exécuter dans votre base de données

-- 1. Créer la table 'like'
CREATE TABLE IF NOT EXISTS `like` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    publication_id INT NOT NULL,
    date_creation DATETIME NOT NULL,
    INDEX IDX_AC6340B3A76ED395 (user_id),
    INDEX IDX_AC6340B338B217AE (publication_id),
    UNIQUE KEY unique_user_publication (user_id, publication_id),
    CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE,
    CONSTRAINT FK_AC6340B338B217AE FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Créer la table 'notification'
CREATE TABLE IF NOT EXISTS notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinataire_id INT NOT NULL,
    auteur_id INT NOT NULL,
    publication_id INT DEFAULT NULL,
    commentaire_id INT DEFAULT NULL,
    type VARCHAR(50) NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL,
    INDEX IDX_BF5476CAA76ED395 (destinataire_id),
    INDEX IDX_BF5476CAA76ED395_2 (auteur_id),
    INDEX IDX_BF5476CA38B217AE (publication_id),
    INDEX IDX_BF5476CABA9CD190 (commentaire_id),
    CONSTRAINT FK_BF5476CA_destinataire FOREIGN KEY (destinataire_id) REFERENCES user (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_auteur FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_publication FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_commentaire FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Modifier la table 'commentaire' pour ajouter les réponses imbriquées
ALTER TABLE commentaire 
ADD COLUMN IF NOT EXISTS parent_id INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS date_modification DATETIME DEFAULT NULL,
ADD INDEX IF NOT EXISTS IDX_67F068BC727ACA70 (parent_id),
ADD CONSTRAINT FK_67F068BC727ACA70 FOREIGN KEY (parent_id) REFERENCES commentaire (id) ON DELETE CASCADE;

-- Note: Si vous utilisez MySQL 5.7 ou antérieur, utilisez cette syntaxe à la place:
-- ALTER TABLE commentaire ADD COLUMN parent_id INT DEFAULT NULL;
-- ALTER TABLE commentaire ADD COLUMN date_modification DATETIME DEFAULT NULL;
-- ALTER TABLE commentaire ADD INDEX IDX_67F068BC727ACA70 (parent_id);
-- ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC727ACA70 FOREIGN KEY (parent_id) REFERENCES commentaire (id) ON DELETE CASCADE;

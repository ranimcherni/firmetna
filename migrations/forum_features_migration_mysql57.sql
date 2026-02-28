-- Migration pour MySQL 5.7 et versions antérieures
-- (sans IF NOT EXISTS et ADD COLUMN IF NOT EXISTS)

-- 1. Créer la table 'like'
CREATE TABLE `like` (
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
CREATE TABLE notification (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinataire_id INT NOT NULL,
    auteur_id INT NOT NULL,
    publication_id INT DEFAULT NULL,
    commentaire_id INT DEFAULT NULL,
    type VARCHAR(50) NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL,
    INDEX IDX_BF5476CA_destinataire (destinataire_id),
    INDEX IDX_BF5476CA_auteur (auteur_id),
    INDEX IDX_BF5476CA_publication (publication_id),
    INDEX IDX_BF5476CA_commentaire (commentaire_id),
    CONSTRAINT FK_BF5476CA_destinataire FOREIGN KEY (destinataire_id) REFERENCES user (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_auteur FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_publication FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE,
    CONSTRAINT FK_BF5476CA_commentaire FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Vérifier si les colonnes existent avant de les ajouter
-- Pour parent_id
SET @dbname = DATABASE();
SET @tablename = 'commentaire';
SET @columnname = 'parent_id';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (table_name = @tablename)
            AND (table_schema = @dbname)
            AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' INT DEFAULT NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Pour date_modification
SET @columnname = 'date_modification';
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE
            (table_name = @tablename)
            AND (table_schema = @dbname)
            AND (column_name = @columnname)
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DATETIME DEFAULT NULL')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Ajouter l'index et la contrainte pour parent_id si elle n'existe pas
SET @preparedStatement = (SELECT IF(
    (
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            (TABLE_SCHEMA = @dbname)
            AND (TABLE_NAME = @tablename)
            AND (CONSTRAINT_NAME = 'FK_67F068BC727ACA70')
    ) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE ', @tablename, ' ADD INDEX IDX_67F068BC727ACA70 (parent_id), ADD CONSTRAINT FK_67F068BC727ACA70 FOREIGN KEY (parent_id) REFERENCES commentaire (id) ON DELETE CASCADE')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

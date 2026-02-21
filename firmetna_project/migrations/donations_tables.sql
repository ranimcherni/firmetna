-- Module Dons : à exécuter sur la base firmetna_new_db (ou celle utilisée par le projet)
-- Création des tables offre et demande

CREATE TABLE IF NOT EXISTS offre (
    id INT AUTO_INCREMENT NOT NULL,
    auteur_id INT DEFAULT NULL,
    nom VARCHAR(150) NOT NULL,
    telephone VARCHAR(25) NOT NULL,
    categorie VARCHAR(100) NOT NULL,
    description LONGTEXT NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    quantite VARCHAR(100) DEFAULT NULL,
    disponible TINYINT(1) DEFAULT 1 NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX IDX_AF86866F60BB6FE6 (auteur_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_offre_auteur FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE SET NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS demande (
    id INT AUTO_INCREMENT NOT NULL,
    offre_id INT NOT NULL,
    demandeur_id INT NOT NULL,
    created_at DATETIME NOT NULL,
    statut VARCHAR(30) DEFAULT 'en_attente' NOT NULL,
    INDEX IDX_demande_offre (offre_id),
    INDEX IDX_demande_demandeur (demandeur_id),
    PRIMARY KEY(id),
    CONSTRAINT FK_demande_offre FOREIGN KEY (offre_id) REFERENCES offre (id) ON DELETE CASCADE,
    CONSTRAINT FK_demande_demandeur FOREIGN KEY (demandeur_id) REFERENCES user (id) ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

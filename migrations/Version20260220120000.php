<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour les fonctionnalités avancées du forum :
 * - Table 'like' pour le système de likes
 * - Table 'notification' pour les notifications
 * - Modifications de la table 'commentaire' pour les réponses imbriquées
 */
final class Version20260220120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add forum advanced features: likes, notifications, nested comments';
    }

    public function up(Schema $schema): void
    {
        // Créer la table 'like'
        $this->addSql('CREATE TABLE `like` (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            publication_id INT NOT NULL,
            date_creation DATETIME NOT NULL,
            INDEX IDX_AC6340B3A76ED395 (user_id),
            INDEX IDX_AC6340B338B217AE (publication_id),
            UNIQUE INDEX unique_user_publication (user_id, publication_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217AE FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');

        // Créer la table 'notification'
        $this->addSql('CREATE TABLE notification (
            id INT AUTO_INCREMENT NOT NULL,
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
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA_destinataire FOREIGN KEY (destinataire_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA_auteur FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA_publication FOREIGN KEY (publication_id) REFERENCES publication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA_commentaire FOREIGN KEY (commentaire_id) REFERENCES commentaire (id) ON DELETE CASCADE');

        // Modifier la table 'commentaire' pour ajouter les réponses imbriquées
        $this->addSql('ALTER TABLE commentaire ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commentaire ADD date_modification DATETIME DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_67F068BC727ACA70 ON commentaire (parent_id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC727ACA70 FOREIGN KEY (parent_id) REFERENCES commentaire (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les contraintes et colonnes de commentaire
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC727ACA70');
        $this->addSql('DROP INDEX IDX_67F068BC727ACA70 ON commentaire');
        $this->addSql('ALTER TABLE commentaire DROP parent_id');
        $this->addSql('ALTER TABLE commentaire DROP date_modification');

        // Supprimer la table notification
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA_destinataire');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA_auteur');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA_publication');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA_commentaire');
        $this->addSql('DROP TABLE notification');

        // Supprimer la table like
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217AE');
        $this->addSql('DROP TABLE `like`');
    }
}

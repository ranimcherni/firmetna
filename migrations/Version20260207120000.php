<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Module Dons : tables offre et demande
 */
final class Version20260207120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Module Dons : offre (offres de don) et demande (demandes des agriculteurs)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE offre (
            id INT AUTO_INCREMENT NOT NULL,
            auteur_id INT DEFAULT NULL,
            nom VARCHAR(150) NOT NULL,
            telephone VARCHAR(25) NOT NULL,
            categorie VARCHAR(100) NOT NULL,
            description LONGTEXT NOT NULL,
            photo VARCHAR(255) DEFAULT NULL,
            quantite VARCHAR(100) DEFAULT NULL,
            disponible TINYINT(1) DEFAULT 1 NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_AF86866F60BB6FE6 (auteur_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande (
            id INT AUTO_INCREMENT NOT NULL,
            offre_id INT NOT NULL,
            demandeur_id INT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            statut VARCHAR(30) DEFAULT \'en_attente\' NOT NULL,
            INDEX IDX_96A678AB4CC8505A (offre_id),
            INDEX IDX_96A678AB95A6EE59 (demandeur_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_96A678AB4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_96A678AB95A6EE59 FOREIGN KEY (demandeur_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F60BB6FE6');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_96A678AB4CC8505A');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_96A678AB95A6EE59');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE demande');
    }
}

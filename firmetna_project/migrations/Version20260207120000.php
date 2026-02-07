<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260207120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add produit, commande, ligne_commande tables for Gestion des produits.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, type VARCHAR(20) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, unite VARCHAR(30) NOT NULL, stock INT DEFAULT 0 NOT NULL, is_bio TINYINT(1) DEFAULT 0 NOT NULL, badge VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, date_commande DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', statut VARCHAR(30) NOT NULL, adresse_livraison LONGTEXT DEFAULT NULL, total NUMERIC(12, 2) DEFAULT 0 NOT NULL, commentaire VARCHAR(500) DEFAULT NULL, INDEX IDX_6EE2E1FF19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ligne_commande (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, INDEX IDX_6946E3E82BF186E (commande_id), INDEX IDX_6946E3E8F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EE2E1FF19EB6921 FOREIGN KEY (client_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_6946E3E82BF186E FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_6946E3E8F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EE2E1FF19EB6921');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_6946E3E82BF186E');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_6946E3E8F347EFB');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE ligne_commande');
    }
}

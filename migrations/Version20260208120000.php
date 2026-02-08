<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Partners module: partner + partner_offer tables.
 */
final class Version20260208120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add partner and partner_offer tables for Partners module.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE partner (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(100) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(30) DEFAULT NULL,
            address VARCHAR(255) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            website VARCHAR(255) DEFAULT NULL,
            logo_url VARCHAR(255) DEFAULT NULL,
            status VARCHAR(50) DEFAULT NULL,
            created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE partner_offer (
            id INT AUTO_INCREMENT NOT NULL,
            partner_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            amount NUMERIC(12, 2) DEFAULT NULL,
            offer_date DATE DEFAULT NULL,
            status VARCHAR(50) DEFAULT NULL,
            created_at DATETIME DEFAULT NULL,
            INDEX IDX_partner_offer_partner_id (partner_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE partner_offer ADD CONSTRAINT FK_partner_offer_partner FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner_offer DROP FOREIGN KEY FK_partner_offer_partner');
        $this->addSql('DROP TABLE partner_offer');
        $this->addSql('DROP TABLE partner');
    }
}

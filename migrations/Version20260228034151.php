<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228034151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE publication ADD updated_at DATETIME DEFAULT NULL, ADD slug VARCHAR(128) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AF3C6779989D9B62 ON publication (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('DROP INDEX UNIQ_AF3C6779989D9B62 ON publication');
        $this->addSql('ALTER TABLE publication DROP updated_at, DROP slug');
    }
}

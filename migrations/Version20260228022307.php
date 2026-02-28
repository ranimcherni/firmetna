<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228022307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD disponibilite VARCHAR(100) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD latitude NUMERIC(10, 8) DEFAULT NULL, ADD longitude NUMERIC(11, 8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F71F7E88B');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('DROP TABLE participation');
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE lieu DROP disponibilite, DROP description, DROP latitude, DROP longitude');
    }
}

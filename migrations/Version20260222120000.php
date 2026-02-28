<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add quantite_demandee column to demande table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE demande ADD quantite_demandee INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE demande DROP quantite_demandee');
    }
}

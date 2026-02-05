<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout de la colonne image à la table lieu.
 */
final class Version20260205000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne image à la table lieu';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu DROP image');
    }
}

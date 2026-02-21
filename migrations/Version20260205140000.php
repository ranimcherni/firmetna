<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image_filename to publication table';
    }

    public function up(Schema $schema): void
    {
        // La colonne image_filename existe déjà dans la table publication.
        // On laisse cette migration vide pour éviter l'erreur "Duplicate column name".
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE publication DROP image_filename');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add bio, specialite, localisation, role_type to user table';
    }

    public function up(Schema $schema): void
    {
        // Les colonnes bio, specialite, localisation et role_type existent déjà dans la base.
        // On laisse cette migration vide pour éviter l'erreur "Duplicate column name".
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP bio, DROP specialite, DROP localisation, DROP role_type');
    }
}

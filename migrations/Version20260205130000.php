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
        $this->addSql('ALTER TABLE user ADD bio LONGTEXT DEFAULT NULL, ADD specialite VARCHAR(100) DEFAULT NULL, ADD localisation VARCHAR(150) DEFAULT NULL, ADD role_type VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP bio, DROP specialite, DROP localisation, DROP role_type');
    }
}

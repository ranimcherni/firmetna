<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add disponibilite and description columns to lieu table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu ADD disponibilite VARCHAR(100) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu DROP disponibilite, DROP description');
    }
}

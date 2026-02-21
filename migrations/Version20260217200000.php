<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217200000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add latitude and longitude columns to lieu table for geolocation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu ADD latitude NUMERIC(10, 8) DEFAULT NULL, ADD longitude NUMERIC(11, 8) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lieu DROP latitude, DROP longitude');
    }
}

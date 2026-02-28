<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217191000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create participation table for event-user relationship';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE participation (
            id INT AUTO_INCREMENT NOT NULL,
            event_id INT NOT NULL,
            user_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_AB55E24F71F7E88B (event_id),
            INDEX IDX_AB55E24FA76ED395 (user_id),
            UNIQUE INDEX uniq_event_user (event_id, user_id),
            PRIMARY KEY(id),
            CONSTRAINT FK_AB55E24F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE,
            CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE participation');
    }
}

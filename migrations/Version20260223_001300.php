<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223_001300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add email column to commande table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('commande');
        
        // Add email column if it doesn't exist
        if (!$table->hasColumn('email')) {
            $table->addColumn('email', 'string', [
                'length' => 255,
                'notnull' => true
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('commande');
        
        if ($table->hasColumn('email')) {
            $table->dropColumn('email');
        }
    }
}

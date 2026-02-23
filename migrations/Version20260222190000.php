<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260222190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add facial recognition fields to user table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');
        
        // Ajouter les champs de reconnaissance faciale
        if (!$table->hasColumn('face_data')) {
            $table->addColumn('face_data', 'text', [
                'notnull' => true,
                'comment' => 'Facial recognition data in JSON format'
            ]);
        }
        
        if (!$table->hasColumn('facial_recognition_enabled')) {
            $table->addColumn('facial_recognition_enabled', 'boolean', [
                'notnull' => false,
                'default' => false,
                'comment' => 'Whether facial recognition is enabled for this user'
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('user');
        
        // Supprimer les champs de reconnaissance faciale
        if ($table->hasColumn('face_data')) {
            $table->dropColumn('face_data');
        }
        
        if ($table->hasColumn('facial_recognition_enabled')) {
            $table->dropColumn('facial_recognition_enabled');
        }
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260223_001200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create email_verification table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('email_verification');
        
        $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
        $table->addColumn('email', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('code', 'string', ['length' => 6, 'notnull' => true]);
        $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
        $table->addColumn('expires_at', 'datetime_immutable', ['notnull' => true]);
        $table->addColumn('is_verified', 'boolean', ['notnull' => true, 'default' => false]);
        $table->addColumn('verified_at', 'datetime_immutable', ['notnull' => true]);
        $table->addColumn('commande_id', 'integer', ['notnull' => true]);
        
        $table->setPrimaryKey(['id']);
        $table->addIndex(['email'], 'idx_email_verification_email');
        $table->addIndex(['is_verified', 'expires_at'], 'idx_email_verification_valid');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('email_verification');
    }
}

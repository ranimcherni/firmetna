<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260228134113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contract (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, amount NUMERIC(12, 2) DEFAULT NULL, offer_date DATE DEFAULT NULL, date_fin_contract DATE DEFAULT NULL, date_debut_contract DATE DEFAULT NULL, status VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL, partner_id INT NOT NULL, INDEX IDX_E98F28599393F8FE (partner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE email_verification (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, code VARCHAR(6) NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, is_verified TINYINT NOT NULL, verified_at DATETIME DEFAULT NULL, commande_id INT DEFAULT NULL, INDEX IDX_FE2235882EA2E54 (commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_AB55E24F71F7E88B (event_id), INDEX IDX_AB55E24FA76ED395 (user_id), UNIQUE INDEX uniq_event_user (event_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contract ADD CONSTRAINT FK_E98F28599393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE2235882EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F71F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE demande ADD quantite_demandee INT NOT NULL');
        $this->addSql('ALTER TABLE lieu ADD disponibilite VARCHAR(100) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD latitude NUMERIC(10, 8) DEFAULT NULL, ADD longitude NUMERIC(11, 8) DEFAULT NULL');
        $this->addSql('ALTER TABLE offre DROP nom');
        $this->addSql('DROP INDEX UNIQ_AF3C6779989D9B62 ON publication');
        $this->addSql('ALTER TABLE publication DROP updated_at, DROP slug');
        $this->addSql('ALTER TABLE user ADD face_signature LONGTEXT DEFAULT NULL, ADD facial_recognition_enabled TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contract DROP FOREIGN KEY FK_E98F28599393F8FE');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE2235882EA2E54');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F71F7E88B');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('DROP TABLE contract');
        $this->addSql('DROP TABLE email_verification');
        $this->addSql('DROP TABLE participation');
        $this->addSql('ALTER TABLE commande CHANGE total total NUMERIC(12, 2) DEFAULT \'0.00\' NOT NULL');
        $this->addSql('ALTER TABLE demande DROP quantite_demandee');
        $this->addSql('ALTER TABLE lieu DROP disponibilite, DROP description, DROP latitude, DROP longitude');
        $this->addSql('ALTER TABLE offre ADD nom VARCHAR(150) NOT NULL');
        $this->addSql('ALTER TABLE publication ADD updated_at DATETIME DEFAULT NULL, ADD slug VARCHAR(128) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AF3C6779989D9B62 ON publication (slug)');
        $this->addSql('ALTER TABLE user DROP face_signature, DROP facial_recognition_enabled');
    }
}

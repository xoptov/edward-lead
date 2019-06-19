<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190619122532 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(32) NOT NULL, CHANGE office_phone office_phone VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD external_id VARCHAR(16) NOT NULL, ADD started_at DATETIME DEFAULT NULL, ADD answer_at DATETIME DEFAULT NULL, ADD completed_at DATETIME DEFAULT NULL, ADD status VARCHAR(9) DEFAULT NULL, ADD bill_secs INT DEFAULT NULL, ADD record VARCHAR(255) DEFAULT NULL, CHANGE caller_id caller_id INT UNSIGNED DEFAULT NULL, CHANGE lead_id lead_id INT UNSIGNED DEFAULT NULL, CHANGE duration_in_secs duration_in_secs INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2C55458D FOREIGN KEY (lead_id) REFERENCES lead (id)');
        $this->addSql('INSERT INTO account(balance, type, description, enabled) VALUES(0, \'system\', \'для телефонии\', 1)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM account WHERE type = \'system\' AND description LIKE \'%для%телефонии%\'');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(12) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE phone_call DROP external_id, DROP started_at, DROP answer_at, DROP completed_at, DROP status, DROP bill_secs, DROP record, CHANGE caller_id caller_id INT UNSIGNED NOT NULL, CHANGE lead_id lead_id INT UNSIGNED NOT NULL, CHANGE duration_in_secs duration_in_secs INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
    }
}

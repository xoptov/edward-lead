<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190617125031 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(32) NOT NULL, CHANGE office_phone office_phone VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('ALTER TABLE phone_call ADD external_id VARCHAR(16) NOT NULL, ADD started_at DATETIME DEFAULT NULL, ADD answer_at DATETIME DEFAULT NULL, ADD completed_at DATETIME DEFAULT NULL, ADD status VARCHAR(9) DEFAULT NULL, ADD bill_secs INT DEFAULT NULL, ADD record VARCHAR(255) DEFAULT NULL, CHANGE lead_id lead_id INT UNSIGNED DEFAULT NULL, CHANGE caller_id caller_id INT UNSIGNED DEFAULT NULL, CHANGE duration_in_secs duration_in_secs INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2C55458D FOREIGN KEY (lead_id) REFERENCES lead (id)');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(12) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE phone_call DROP external_id, DROP started_at, DROP answer_at, DROP completed_at, DROP status, DROP bill_secs, DROP record, CHANGE caller_id caller_id INT UNSIGNED NOT NULL, CHANGE lead_id lead_id INT UNSIGNED NOT NULL, CHANGE duration_in_secs duration_in_secs INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2C55458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
    }
}

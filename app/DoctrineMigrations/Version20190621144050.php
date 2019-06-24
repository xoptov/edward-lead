<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190621144050 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pbx_callback (id INT UNSIGNED AUTO_INCREMENT NOT NULL, phone_call_id INT UNSIGNED DEFAULT NULL, description VARCHAR(6) DEFAULT NULL, src_phone_number VARCHAR(11) DEFAULT NULL, dst_phone_number VARCHAR(11) DEFAULT NULL, started_at DATETIME NOT NULL, answer_at DATETIME NOT NULL, completed_at DATETIME NOT NULL, direction VARCHAR(8) NOT NULL, status VARCHAR(10) NOT NULL, duration INT UNSIGNED NOT NULL, billsec INT UNSIGNED NOT NULL, recording VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_38988E6C0EA171E (phone_call_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pbx_callback ADD CONSTRAINT FK_38988E6C0EA171E FOREIGN KEY (phone_call_id) REFERENCES phone_call (id)');
        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(32) NOT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(32) NOT NULL, CHANGE office_phone office_phone VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD external_id VARCHAR(16) NOT NULL, ADD status VARCHAR(9) DEFAULT NULL, DROP duration_in_secs, CHANGE caller_id caller_id INT UNSIGNED DEFAULT NULL, CHANGE lead_id lead_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2C55458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE SET NULL');
        $this->addSql('INSERT INTO account(balance, type, description, enabled) VALUES(0, \'system\', \'для телефонии\', 1)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM account WHERE type=\'system\' AND description LIKE \'%для%телефонии%\'');
        $this->addSql('DROP TABLE pbx_callback');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, CHANGE office_phone office_phone VARCHAR(12) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE lead CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE operation CHANGE amount amount INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE phone_call ADD duration_in_secs INT UNSIGNED NOT NULL, DROP external_id, DROP status, CHANGE caller_id caller_id INT UNSIGNED NOT NULL, CHANGE lead_id lead_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE phone phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci');
    }
}

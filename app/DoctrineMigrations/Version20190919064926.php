<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190919064926 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pbx_callback DROP INDEX UNIQ_38988E6C0EA171E, ADD INDEX IDX_38988E6C0EA171E (phone_call_id)');
        $this->addSql('ALTER TABLE pbx_callback DROP FOREIGN KEY FK_38988E6C0EA171E');
        $this->addSql('ALTER TABLE pbx_callback ADD event VARCHAR(6) NOT NULL, ADD audio_record VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD first_shoulder_phone VARCHAR(32) DEFAULT NULL, ADD first_shoulder_tariff VARCHAR(16) DEFAULT NULL, ADD first_shoulder_start_at DATETIME DEFAULT NULL, ADD first_shoulder_answer_at DATETIME DEFAULT NULL, ADD first_shoulder_hangup_at DATETIME DEFAULT NULL, ADD first_shoulder_bill_sec INT DEFAULT NULL, ADD first_shoulder_status VARCHAR(16) DEFAULT NULL, ADD second_shoulder_phone VARCHAR(32) DEFAULT NULL, ADD second_shoulder_tariff VARCHAR(16) DEFAULT NULL, ADD second_shoulder_start_at DATETIME DEFAULT NULL, ADD second_shoulder_answer_at DATETIME DEFAULT NULL, ADD second_shoulder_hangup_at DATETIME DEFAULT NULL, ADD second_shoulder_bill_sec INT DEFAULT NULL, ADD second_shoulder_status VARCHAR(16) DEFAULT NULL, DROP description, DROP src_phone_number, DROP dst_phone_number, DROP started_at, DROP answer_at, DROP completed_at, DROP direction, DROP duration, DROP billsec, DROP recording, CHANGE phone_call_id phone_call_id INT UNSIGNED NOT NULL, CHANGE status status SMALLINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE pbx_callback ADD CONSTRAINT FK_38988E6C0EA171E FOREIGN KEY (phone_call_id) REFERENCES phone_call (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('DROP INDEX IDX_2F8A7D2C55458D ON phone_call');
        $this->addSql('ALTER TABLE phone_call ADD result SMALLINT UNSIGNED DEFAULT NULL, CHANGE caller_id caller_id INT UNSIGNED NOT NULL, CHANGE external_id external_id VARCHAR(16) NOT NULL, CHANGE lead_id trade_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CC2D9760 FOREIGN KEY (trade_id) REFERENCES trade (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2F8A7D2CC2D9760 ON phone_call (trade_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pbx_callback DROP INDEX IDX_38988E6C0EA171E, ADD UNIQUE INDEX UNIQ_38988E6C0EA171E (phone_call_id)');
        $this->addSql('ALTER TABLE pbx_callback DROP FOREIGN KEY FK_38988E6C0EA171E');
        $this->addSql('ALTER TABLE pbx_callback ADD description VARCHAR(6) DEFAULT NULL COLLATE utf8_unicode_ci, ADD src_phone_number VARCHAR(11) DEFAULT NULL COLLATE utf8_unicode_ci, ADD dst_phone_number VARCHAR(11) DEFAULT NULL COLLATE utf8_unicode_ci, ADD answer_at DATETIME NOT NULL, ADD completed_at DATETIME NOT NULL, ADD direction VARCHAR(8) NOT NULL COLLATE utf8_unicode_ci, ADD duration INT UNSIGNED NOT NULL, ADD billsec INT UNSIGNED NOT NULL, ADD recording VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP event, DROP audio_record, DROP first_shoulder_phone, DROP first_shoulder_tariff, DROP first_shoulder_start_at, DROP first_shoulder_answer_at, DROP first_shoulder_hangup_at, DROP first_shoulder_bill_sec, DROP first_shoulder_status, DROP second_shoulder_phone, DROP second_shoulder_tariff, DROP second_shoulder_start_at, DROP second_shoulder_answer_at, DROP second_shoulder_hangup_at, DROP second_shoulder_bill_sec, DROP second_shoulder_status, CHANGE phone_call_id phone_call_id INT UNSIGNED DEFAULT NULL, CHANGE status status VARCHAR(10) NOT NULL COLLATE utf8_unicode_ci, CHANGE created_at started_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE pbx_callback ADD CONSTRAINT FK_38988E6C0EA171E FOREIGN KEY (phone_call_id) REFERENCES phone_call (id)');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CC2D9760');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CA5626C52');
        $this->addSql('DROP INDEX IDX_2F8A7D2CC2D9760 ON phone_call');
        $this->addSql('ALTER TABLE phone_call DROP result, CHANGE caller_id caller_id INT UNSIGNED DEFAULT NULL, CHANGE external_id external_id VARCHAR(16) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE trade_id lead_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2C55458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2F8A7D2C55458D ON phone_call (lead_id)');
    }
}

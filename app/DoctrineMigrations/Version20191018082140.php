<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191018082140 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE room ADD timer TINYINT(1) NOT NULL, ADD schedule_start_hour SMALLINT DEFAULT NULL, ADD schedule_end_hour SMALLINT DEFAULT NULL, ADD schedule_days_of_week LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', ADD schedule_execution_hours SMALLINT DEFAULT NULL, ADD schedule_leads_per_day SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD timer_timeout_at DATETIME DEFAULT NULL, ADD timer_action VARCHAR(255) DEFAULT NULL, DROP expiration_date');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead ADD expiration_date DATETIME NOT NULL, DROP timer_timeout_at, DROP timer_action');
        $this->addSql('ALTER TABLE room DROP timer, DROP schedule_start_hour, DROP schedule_end_hour, DROP schedule_days_of_week, DROP schedule_execution_hours, DROP schedule_leads_per_day');
    }
}

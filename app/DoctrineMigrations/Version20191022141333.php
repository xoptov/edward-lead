<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191022141333 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city ADD timezone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD timer TINYINT(1) NOT NULL, ADD execution_hours SMALLINT UNSIGNED DEFAULT NULL, ADD leads_per_day SMALLINT UNSIGNED DEFAULT NULL, ADD schedule_work_days SMALLINT UNSIGNED DEFAULT NULL, ADD schedule_work_time_start_at TIME DEFAULT NULL, ADD schedule_work_time_end_at TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD timer_end_at DATETIME DEFAULT NULL, ADD timer_action VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE city DROP timezone');
        $this->addSql('ALTER TABLE lead DROP timer_end_at, DROP timer_action');
        $this->addSql('ALTER TABLE room DROP timer, DROP execution_hours, DROP leads_per_day, DROP schedule_work_days, DROP schedule_work_time_start_at, DROP schedule_work_time_end_at');
    }
}

<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191025073150 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE room ADD city_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_729F519B8BAC62AF ON room (city_id)');
        $this->addSql('ALTER TABLE lead ADD timer_processed_at DATETIME DEFAULT NULL, DROP timer_action');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead ADD timer_action VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP timer_processed_at');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B8BAC62AF');
        $this->addSql('DROP INDEX IDX_729F519B8BAC62AF ON room');
        $this->addSql('ALTER TABLE room DROP city_id');
    }
}

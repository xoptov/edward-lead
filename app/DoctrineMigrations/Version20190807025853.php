<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190807025853 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE thread ADD seller_thread_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE thread ADD CONSTRAINT FK_31204C833F4B32F FOREIGN KEY (seller_thread_id) REFERENCES thread (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31204C833F4B32F ON thread (seller_thread_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C833F4B32F');
        $this->addSql('DROP INDEX UNIQ_31204C833F4B32F ON thread');
        $this->addSql('ALTER TABLE thread DROP seller_thread_id');
    }
}

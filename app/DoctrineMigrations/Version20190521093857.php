<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190521093857 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead ADD user_id INT UNSIGNED DEFAULT NULL, ADD audio_record VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_289161CBA76ED395 ON lead (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CBA76ED395');
        $this->addSql('DROP INDEX IDX_289161CBA76ED395 ON lead');
        $this->addSql('ALTER TABLE lead DROP user_id, DROP audio_record');
    }
}

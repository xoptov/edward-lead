<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190515095918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB4A913F18');
        $this->addSql('DROP INDEX IDX_289161CB4A913F18 ON lead');
        $this->addSql('ALTER TABLE lead CHANGE advertising_channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB72F5A1AA FOREIGN KEY (channel_id) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_289161CB72F5A1AA ON lead (channel_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB72F5A1AA');
        $this->addSql('DROP INDEX IDX_289161CB72F5A1AA ON lead');
        $this->addSql('ALTER TABLE lead CHANGE channel_id advertising_channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB4A913F18 FOREIGN KEY (advertising_channel_id) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_289161CB4A913F18 ON lead (advertising_channel_id)');
    }
}

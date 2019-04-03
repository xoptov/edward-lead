<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190403092917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD name VARCHAR(30) NOT NULL, ADD phone VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE webmaster CHANGE phone phone VARCHAR(12) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE company CHANGE phone phone VARCHAR(11) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user DROP name, DROP phone');
        $this->addSql('ALTER TABLE webmaster CHANGE phone phone VARCHAR(11) NOT NULL COLLATE utf8_unicode_ci');
    }
}

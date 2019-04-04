<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190404141637 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE webmaster');
        $this->addSql('ALTER TABLE region ADD parent_id SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176727ACA70 FOREIGN KEY (parent_id) REFERENCES region (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F62F176727ACA70 ON region (parent_id)');
        $this->addSql('ALTER TABLE user ADD skype VARCHAR(30) DEFAULT NULL, ADD vkontakte VARCHAR(50) DEFAULT NULL, ADD facebook VARCHAR(50) DEFAULT NULL, ADD telegram VARCHAR(30) DEFAULT NULL, ADD type_selected TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649DDB0A2FE ON user (skype)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491AF09BB7 ON user (vkontakte)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6496B74C8E0 ON user (facebook)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64943320DA ON user (telegram)');
        $this->addSql('DROP INDEX UNIQ_4FBF094FE93323CB ON company');
        $this->addSql('DROP INDEX UNIQ_4FBF094FB89AB2C7 ON company');
        $this->addSql('DROP INDEX UNIQ_4FBF094FB1A4D127 ON company');
        $this->addSql('ALTER TABLE company ADD email VARCHAR(30) NOT NULL, ADD office_name VARCHAR(30) NOT NULL, ADD office_phone VARCHAR(12) NOT NULL, ADD office_address VARCHAR(150) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE webmaster (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, first_name VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci, skype VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, vkontakte VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, facebook VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, phone VARCHAR(12) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_6A224BD4A76ED395 (user_id), UNIQUE INDEX UNIQ_6A224BD4DDB0A2FE (skype), UNIQUE INDEX UNIQ_6A224BD41AF09BB7 (vkontakte), UNIQUE INDEX UNIQ_6A224BD46B74C8E0 (facebook), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE webmaster ADD CONSTRAINT FK_6A224BD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company DROP email, DROP office_name, DROP office_phone, DROP office_address');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FE93323CB ON company (inn)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FB89AB2C7 ON company (ogrn)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094FB1A4D127 ON company (account_number)');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176727ACA70');
        $this->addSql('DROP INDEX IDX_F62F176727ACA70 ON region');
        $this->addSql('ALTER TABLE region DROP parent_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649DDB0A2FE ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6491AF09BB7 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D6496B74C8E0 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D64943320DA ON user');
        $this->addSql('ALTER TABLE user DROP skype, DROP vkontakte, DROP facebook, DROP telegram, DROP type_selected');
    }
}

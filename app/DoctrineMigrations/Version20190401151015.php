<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190401151015 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE region (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F62F1765E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(12) NOT NULL, email VARCHAR(30) NOT NULL, phone VARCHAR(11) DEFAULT NULL, physical_person VARCHAR(30) DEFAULT NULL, organization VARCHAR(30) DEFAULT NULL, inn VARCHAR(12) DEFAULT NULL, ogrn VARCHAR(13) DEFAULT NULL, kpp VARCHAR(9) DEFAULT NULL, skype VARCHAR(30) DEFAULT NULL, vkontakte VARCHAR(50) DEFAULT NULL, facebook VARCHAR(50) DEFAULT NULL, password VARCHAR(60) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649444F97DD (phone), UNIQUE INDEX UNIQ_8D93D649C1EE637C (organization), UNIQUE INDEX UNIQ_8D93D649E93323CB (inn), UNIQUE INDEX UNIQ_8D93D649B89AB2C7 (ogrn), UNIQUE INDEX UNIQ_8D93D649C4F9F519 (kpp), UNIQUE INDEX UNIQ_8D93D649DDB0A2FE (skype), UNIQUE INDEX UNIQ_8D93D6491AF09BB7 (vkontakte), UNIQUE INDEX UNIQ_8D93D6496B74C8E0 (facebook), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE user');
    }
}

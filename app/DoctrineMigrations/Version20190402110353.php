<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190402110353 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE region (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F62F1765E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT UNSIGNED AUTO_INCREMENT NOT NULL, path VARCHAR(150) NOT NULL, filename VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_C53D045FB548B0F (path), UNIQUE INDEX UNIQ_C53D045F3C0BE965 (filename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT UNSIGNED AUTO_INCREMENT NOT NULL, email VARCHAR(30) NOT NULL, password VARCHAR(60) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, logotype_id INT UNSIGNED DEFAULT NULL, short_name VARCHAR(30) NOT NULL, large_name VARCHAR(60) NOT NULL, phone VARCHAR(11) NOT NULL, inn VARCHAR(12) NOT NULL, ogrn VARCHAR(13) NOT NULL, kpp VARCHAR(9) NOT NULL, bik VARCHAR(9) NOT NULL, account_number VARCHAR(25) NOT NULL, address VARCHAR(150) NOT NULL, zipcode VARCHAR(6) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094FE93323CB (inn), UNIQUE INDEX UNIQ_4FBF094FB89AB2C7 (ogrn), UNIQUE INDEX UNIQ_4FBF094FB1A4D127 (account_number), UNIQUE INDEX UNIQ_4FBF094FA76ED395 (user_id), UNIQUE INDEX UNIQ_4FBF094F784CE779 (logotype_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companies_regions (company_id INT UNSIGNED NOT NULL, region_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_7E16F093979B1AD6 (company_id), INDEX IDX_7E16F09398260155 (region_id), PRIMARY KEY(company_id, region_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE webmaster (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, first_name VARCHAR(30) NOT NULL, phone VARCHAR(11) NOT NULL, skype VARCHAR(30) DEFAULT NULL, vkontakte VARCHAR(50) DEFAULT NULL, facebook VARCHAR(50) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_6A224BD4DDB0A2FE (skype), UNIQUE INDEX UNIQ_6A224BD41AF09BB7 (vkontakte), UNIQUE INDEX UNIQ_6A224BD46B74C8E0 (facebook), UNIQUE INDEX UNIQ_6A224BD4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F784CE779 FOREIGN KEY (logotype_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE companies_regions ADD CONSTRAINT FK_7E16F093979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE companies_regions ADD CONSTRAINT FK_7E16F09398260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE webmaster ADD CONSTRAINT FK_6A224BD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE companies_regions DROP FOREIGN KEY FK_7E16F09398260155');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F784CE779');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FA76ED395');
        $this->addSql('ALTER TABLE webmaster DROP FOREIGN KEY FK_6A224BD4A76ED395');
        $this->addSql('ALTER TABLE companies_regions DROP FOREIGN KEY FK_7E16F093979B1AD6');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE companies_regions');
        $this->addSql('DROP TABLE webmaster');
    }
}

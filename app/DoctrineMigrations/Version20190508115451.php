<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190508115451 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, region_id SMALLINT UNSIGNED NOT NULL, name VARCHAR(30) NOT NULL, lead_price INT UNSIGNED DEFAULT NULL, star_price INT UNSIGNED DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2D5B023498260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5373C9665E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(19) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE companies_cities (company_id INT UNSIGNED NOT NULL, city_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_7ED91E45979B1AD6 (company_id), INDEX IDX_7ED91E458BAC62AF (city_id), PRIMARY KEY(company_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE companies_cities ADD CONSTRAINT FK_7ED91E45979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE companies_cities ADD CONSTRAINT FK_7ED91E458BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE companies_regions');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176727ACA70');
        $this->addSql('DROP INDEX IDX_F62F176727ACA70 ON region');
        $this->addSql('ALTER TABLE region CHANGE parent_id country_id SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('CREATE INDEX IDX_F62F176F92F3E70 ON region (country_id)');
        $this->addSql('ALTER TABLE lead ADD city_id SMALLINT UNSIGNED NOT NULL, ADD advertising_channel_id INT DEFAULT NULL, ADD phone VARCHAR(12) NOT NULL, ADD name VARCHAR(30) DEFAULT NULL, ADD order_date DATE DEFAULT NULL, ADD decision_maker TINYINT(1) DEFAULT NULL, ADD made_measurement TINYINT(1) DEFAULT NULL, ADD interest_assessment SMALLINT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD expiration_date DATETIME NOT NULL, ADD status VARCHAR(255) NOT NULL, ADD price INT UNSIGNED NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB4A913F18 FOREIGN KEY (advertising_channel_id) REFERENCES property (id)');
        $this->addSql('CREATE INDEX IDX_289161CB8BAC62AF ON lead (city_id)');
        $this->addSql('CREATE INDEX IDX_289161CB4A913F18 ON lead (advertising_channel_id)');
        $this->addSql('ALTER TABLE user ADD purchase_fee_fixed INT DEFAULT NULL, ADD purchase_fee_percent DOUBLE PRECISION DEFAULT NULL, ADD sale_lead_limit INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB8BAC62AF');
        $this->addSql('ALTER TABLE companies_cities DROP FOREIGN KEY FK_7ED91E458BAC62AF');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176F92F3E70');
        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB4A913F18');
        $this->addSql('CREATE TABLE companies_regions (company_id INT UNSIGNED NOT NULL, region_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_7E16F093979B1AD6 (company_id), INDEX IDX_7E16F09398260155 (region_id), PRIMARY KEY(company_id, region_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE companies_regions ADD CONSTRAINT FK_7E16F093979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE companies_regions ADD CONSTRAINT FK_7E16F09398260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE companies_cities');
        $this->addSql('DROP INDEX IDX_289161CB8BAC62AF ON lead');
        $this->addSql('DROP INDEX IDX_289161CB4A913F18 ON lead');
        $this->addSql('ALTER TABLE lead DROP city_id, DROP advertising_channel_id, DROP phone, DROP name, DROP order_date, DROP decision_maker, DROP made_measurement, DROP interest_assessment, DROP description, DROP expiration_date, DROP status, DROP price, DROP created_at, DROP updated_at');
        $this->addSql('DROP INDEX IDX_F62F176F92F3E70 ON region');
        $this->addSql('ALTER TABLE region CHANGE country_id parent_id SMALLINT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176727ACA70 FOREIGN KEY (parent_id) REFERENCES region (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_F62F176727ACA70 ON region (parent_id)');
        $this->addSql('ALTER TABLE user DROP purchase_fee_fixed, DROP purchase_fee_percent, DROP sale_lead_limit');
    }
}

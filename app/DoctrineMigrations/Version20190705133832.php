<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190705133832 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE room (id INT UNSIGNED AUTO_INCREMENT NOT NULL, owner_id INT UNSIGNED DEFAULT NULL, name VARCHAR(255) NOT NULL, sphere VARCHAR(255) NOT NULL, lead_criteria LONGTEXT DEFAULT NULL, lead_price INT DEFAULT NULL, platform_warranty TINYINT(1) NOT NULL, invite_token VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_729F519B7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1000');
        $this->addSql('CREATE TABLE member (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_70E4FA7854177093 (room_id), INDEX IDX_70E4FA78A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7854177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB8BAC62AF, DROP INDEX IDX_289161CB8BAC62AF, CHANGE city_id city_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE companies_cities DROP FOREIGN KEY FK_7ED91E458BAC62AF, DROP INDEX  IDX_7ED91E458BAC62AF, CHANGE city_id city_id INT UNSIGNED');
        $this->addSql('ALTER TABLE city CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB8BAC62AF FOREIGN KEY(city_id) REFERENCES city(id), ADD INDEX IDX_289161CB8BAC62AF(city_id)');
        $this->addSql('ALTER TABLE companies_cities ADD CONSTRAINT FK_7ED91E458BAC62AF FOREIGN KEY(city_id) REFERENCES city(id) ON DELETE CASCADE, ADD INDEX IDX_7ED91E458BAC62AF(city_id)');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B023498260155, DROP INDEX IDX_2D5B023498260155');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176F92F3E70, DROP INDEX IDX_F62F176F92F3E70');
        $this->addSql('ALTER TABLE country CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE region CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE country_id country_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY(country_id) REFERENCES country(id), ADD INDEX IDX_F62F176F92F3E70(country_id)');
        $this->addSql('ALTER TABLE city CHANGE region_id region_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023498260155 FOREIGN KEY (region_id) REFERENCES region(id), ADD INDEX IDX_2D5B023498260155(region_id)');
        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB72F5A1AA, DROP INDEX IDX_289161CB72F5A1AA, CHANGE channel_id channel_id INT UNSIGNED');
        $this->addSql('ALTER TABLE property CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lead ADD room_id INT UNSIGNED DEFAULT NULL, CHANGE city_id city_id INT UNSIGNED NOT NULL, CHANGE channel_id channel_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB72F5A1AA FOREIGN KEY(channel_id) REFERENCES property(id), ADD INDEX IDX_289161CB72F5A1AA(channel_id)');
        $this->addSql('ALTER TABLE lead ADD CONSTRAINT FK_289161CB54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_289161CB54177093 ON lead (room_id)');
        $this->addSql('ALTER TABLE monetary_transaction CHANGE createdat created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE companies_cities CHANGE city_id city_id INT UNSIGNED NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA7854177093');
        $this->addSql('ALTER TABLE lead DROP FOREIGN KEY FK_289161CB54177093');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE member');
        $this->addSql('ALTER TABLE city CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE region_id region_id SMALLINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE companies_cities CHANGE city_id city_id SMALLINT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE country CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('DROP INDEX IDX_289161CB54177093 ON lead');
        $this->addSql('ALTER TABLE lead DROP room_id, CHANGE city_id city_id SMALLINT UNSIGNED NOT NULL, CHANGE channel_id channel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE monetary_transaction CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE property CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE region CHANGE id id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE country_id country_id SMALLINT UNSIGNED DEFAULT NULL');
    }
}

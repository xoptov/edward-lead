<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191223133135 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE office (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, name VARCHAR(30) DEFAULT NULL, phone VARCHAR(11) DEFAULT NULL, address VARCHAR(150) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_74516B02A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offices_cities (office_id INT UNSIGNED NOT NULL, city_id INT UNSIGNED NOT NULL, INDEX IDX_FAEB42B3FFA0C224 (office_id), INDEX IDX_FAEB42B38BAC62AF (city_id), PRIMARY KEY(office_id, city_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B02A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('INSERT office(user_id, name, phone, address, created_at) SELECT user_id, office_name, office_phone, office_address, NOW() FROM company');
        $this->addSql('ALTER TABLE offices_cities ADD CONSTRAINT FK_FAEB42B3FFA0C224 FOREIGN KEY (office_id) REFERENCES office (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offices_cities ADD CONSTRAINT FK_FAEB42B38BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE companies_cities');
        $this->addSql('ALTER TABLE user ADD logotype_id INT UNSIGNED DEFAULT NULL, ADD type VARCHAR(8) DEFAULT NULL, ADD zipcode VARCHAR(6) DEFAULT NULL, ADD personal_full_name VARCHAR(30) DEFAULT NULL, ADD personal_birth_date DATE DEFAULT NULL, ADD personal_passport_serial VARCHAR(4) DEFAULT NULL, ADD personal_passport_number VARCHAR(6) DEFAULT NULL, ADD personal_passport_issuer VARCHAR(150) DEFAULT NULL, ADD personal_passport_issue_date DATE DEFAULT NULL, ADD personal_passport_address VARCHAR(150) DEFAULT NULL, CHANGE type_selected role_selected TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649784CE779 FOREIGN KEY (logotype_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649784CE779 ON user (logotype_id)');
        $this->addSql('UPDATE user u, company c SET u.logotype_id = c.logotype_id, u.zipcode = c.zipcode WHERE u.id = c.user_id');
        $this->addSql('UPDATE user u, company c SET u.type = \'company\' WHERE u.id = c.user_id');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F784CE779');
        $this->addSql('DROP INDEX UNIQ_4FBF094F784CE779 ON company');
        $this->addSql('ALTER TABLE company DROP logotype_id, DROP zipcode, DROP office_name, DROP office_phone, DROP office_address');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offices_cities DROP FOREIGN KEY FK_FAEB42B3FFA0C224');
        $this->addSql('CREATE TABLE companies_cities (company_id INT UNSIGNED NOT NULL, city_id INT UNSIGNED NOT NULL, INDEX IDX_7ED91E45979B1AD6 (company_id), INDEX IDX_7ED91E458BAC62AF (city_id), PRIMARY KEY(company_id, city_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE companies_cities ADD CONSTRAINT FK_7ED91E458BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE companies_cities ADD CONSTRAINT FK_7ED91E45979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE company ADD logotype_id INT UNSIGNED DEFAULT NULL, ADD zipcode VARCHAR(6) NOT NULL COLLATE utf8_unicode_ci, ADD office_name VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, ADD office_phone VARCHAR(32) DEFAULT NULL COLLATE utf8_unicode_ci, ADD office_address VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F784CE779 FOREIGN KEY (logotype_id) REFERENCES image (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F784CE779 ON company (logotype_id)');
        $this->addSql('UPDATE company c, office o SET c.user_id = o.user_id, c.office_name = o.name, c.office_phone = o.phone, c.office_address = o.address WHERE c.user_id = o.user_id');
        $this->addSql('UPDATE company c, user u SET c.logotype_id = u.logotype_id, c.zipcode = u.zipcode WHERE c.user_id = u.id');
        $this->addSql('DROP TABLE office');
        $this->addSql('DROP TABLE offices_cities');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649784CE779');
        $this->addSql('DROP INDEX UNIQ_8D93D649784CE779 ON user');
        $this->addSql('ALTER TABLE user DROP logotype_id, DROP type, DROP zipcode, DROP personal_full_name, DROP personal_birth_date, DROP personal_passport_serial, DROP personal_passport_number, DROP personal_passport_issuer, DROP personal_passport_issue_date, DROP personal_passport_address, CHANGE role_selected type_selected TINYINT(1) NOT NULL');
    }
}

<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201128205112 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE user ADD organization_short_name VARCHAR(50) DEFAULT NULL, ADD organization_large_name VARCHAR(100) DEFAULT NULL, ADD organization_inn VARCHAR(12) DEFAULT NULL, ADD organization_ogrn VARCHAR(15) DEFAULT NULL, ADD organization_kpp VARCHAR(9) DEFAULT NULL, ADD organization_bik VARCHAR(9) DEFAULT NULL, ADD organization_account_number VARCHAR(25) DEFAULT NULL, ADD organization_address VARCHAR(150) DEFAULT NULL, CHANGE type type VARCHAR(12) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE company (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, short_name VARCHAR(50) DEFAULT NULL COLLATE utf8_unicode_ci, large_name VARCHAR(100) DEFAULT NULL COLLATE utf8_unicode_ci, inn VARCHAR(12) DEFAULT NULL COLLATE utf8_unicode_ci, ogrn VARCHAR(15) DEFAULT NULL COLLATE utf8_unicode_ci, kpp VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci, bik VARCHAR(9) DEFAULT NULL COLLATE utf8_unicode_ci, account_number VARCHAR(25) DEFAULT NULL COLLATE utf8_unicode_ci, address VARCHAR(150) DEFAULT NULL COLLATE utf8_unicode_ci, zipcode VARCHAR(6) DEFAULT NULL COLLATE utf8_unicode_ci, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP organization_short_name, DROP organization_large_name, DROP organization_inn, DROP organization_ogrn, DROP organization_kpp, DROP organization_bik, DROP organization_account_number, DROP organization_address, CHANGE type type VARCHAR(8) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}

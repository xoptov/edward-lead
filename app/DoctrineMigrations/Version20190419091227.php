<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190419091227 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE account (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, balance BIGINT NOT NULL, updated_at DATETIME DEFAULT NULL, type VARCHAR(8) NOT NULL, UNIQUE INDEX UNIQ_7D3656A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description VARCHAR(150) DEFAULT NULL, amount INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, type VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fee (id INT UNSIGNED NOT NULL, operation_id INT UNSIGNED NOT NULL, payer_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_964964B544AC3583 (operation_id), INDEX IDX_964964B5C17AD9A9 (payer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monetary_hold (id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_id INT UNSIGNED NOT NULL, operation_id INT UNSIGNED NOT NULL, amount INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4B1858279B6B5FBA (account_id), UNIQUE INDEX UNIQ_4B18582744AC3583 (operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, status SMALLINT UNSIGNED NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_90651744A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation_confirm (id INT UNSIGNED NOT NULL, author_id INT UNSIGNED DEFAULT NULL, withdraw_id INT UNSIGNED DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, type VARCHAR(9) NOT NULL, INDEX IDX_2C122389F675F31B (author_id), INDEX IDX_2C122389CD84EE37 (withdraw_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lead (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monetary_transaction (id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_id INT UNSIGNED NOT NULL, operation_id INT UNSIGNED NOT NULL, amount INT NOT NULL, processed TINYINT(1) NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_569311C59B6B5FBA (account_id), UNIQUE INDEX UNIQ_569311C544AC3583 (operation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE withdraw (id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B5DE5F9EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trade (id INT UNSIGNED NOT NULL, buyer_id INT UNSIGNED NOT NULL, seller_id INT UNSIGNED NOT NULL, lead_id INT UNSIGNED NOT NULL, status SMALLINT UNSIGNED NOT NULL, INDEX IDX_7E1A43666C755722 (buyer_id), INDEX IDX_7E1A43668DE820D9 (seller_id), UNIQUE INDEX UNIQ_7E1A436655458D (lead_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referrer_reward (id INT UNSIGNED NOT NULL, operation_id INT UNSIGNED NOT NULL, referrer_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_E96D313A44AC3583 (operation_id), INDEX IDX_E96D313A798C22DB (referrer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_call (id INT UNSIGNED NOT NULL, caller_id INT UNSIGNED NOT NULL, lead_id INT UNSIGNED NOT NULL, duration_in_secs INT UNSIGNED NOT NULL, INDEX IDX_2F8A7D2CA5626C52 (caller_id), INDEX IDX_2F8A7D2C55458D (lead_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_964964B544AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_964964B5C17AD9A9 FOREIGN KEY (payer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_964964B5BF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monetary_hold ADD CONSTRAINT FK_4B1858279B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monetary_hold ADD CONSTRAINT FK_4B18582744AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744BF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_confirm ADD CONSTRAINT FK_2C122389F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE operation_confirm ADD CONSTRAINT FK_2C122389CD84EE37 FOREIGN KEY (withdraw_id) REFERENCES withdraw (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE monetary_transaction ADD CONSTRAINT FK_569311C59B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE monetary_transaction ADD CONSTRAINT FK_569311C544AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE withdraw ADD CONSTRAINT FK_B5DE5F9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE withdraw ADD CONSTRAINT FK_B5DE5F9EBF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43666C755722 FOREIGN KEY (buyer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43668DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A436655458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366BF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referrer_reward ADD CONSTRAINT FK_E96D313A44AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referrer_reward ADD CONSTRAINT FK_E96D313A798C22DB FOREIGN KEY (referrer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referrer_reward ADD CONSTRAINT FK_E96D313ABF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone_call ADD CONSTRAINT FK_2F8A7D2CBF396750 FOREIGN KEY (id) REFERENCES operation (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monetary_hold DROP FOREIGN KEY FK_4B1858279B6B5FBA');
        $this->addSql('ALTER TABLE monetary_transaction DROP FOREIGN KEY FK_569311C59B6B5FBA');
        $this->addSql('ALTER TABLE fee DROP FOREIGN KEY FK_964964B544AC3583');
        $this->addSql('ALTER TABLE fee DROP FOREIGN KEY FK_964964B5BF396750');
        $this->addSql('ALTER TABLE monetary_hold DROP FOREIGN KEY FK_4B18582744AC3583');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744BF396750');
        $this->addSql('ALTER TABLE monetary_transaction DROP FOREIGN KEY FK_569311C544AC3583');
        $this->addSql('ALTER TABLE withdraw DROP FOREIGN KEY FK_B5DE5F9EBF396750');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366BF396750');
        $this->addSql('ALTER TABLE referrer_reward DROP FOREIGN KEY FK_E96D313A44AC3583');
        $this->addSql('ALTER TABLE referrer_reward DROP FOREIGN KEY FK_E96D313ABF396750');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2CBF396750');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A436655458D');
        $this->addSql('ALTER TABLE phone_call DROP FOREIGN KEY FK_2F8A7D2C55458D');
        $this->addSql('ALTER TABLE operation_confirm DROP FOREIGN KEY FK_2C122389CD84EE37');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE fee');
        $this->addSql('DROP TABLE monetary_hold');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE operation_confirm');
        $this->addSql('DROP TABLE lead');
        $this->addSql('DROP TABLE monetary_transaction');
        $this->addSql('DROP TABLE withdraw');
        $this->addSql('DROP TABLE trade');
        $this->addSql('DROP TABLE referrer_reward');
        $this->addSql('DROP TABLE phone_call');
    }
}

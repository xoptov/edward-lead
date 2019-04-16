<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190415150605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reason (id INT UNSIGNED AUTO_INCREMENT NOT NULL, description VARCHAR(150) DEFAULT NULL, amount INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, type VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fee (id INT UNSIGNED NOT NULL, reason_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_964964B559BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, status SMALLINT UNSIGNED NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_90651744A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, balance BIGINT NOT NULL, updated_at DATETIME DEFAULT NULL, type VARCHAR(6) NOT NULL, UNIQUE INDEX UNIQ_7D3656A4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_id INT UNSIGNED NOT NULL, reason_id INT UNSIGNED NOT NULL, amount INT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_1981A66D9B6B5FBA (account_id), UNIQUE INDEX UNIQ_1981A66D59BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lead (id INT UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hold (id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_id INT UNSIGNED NOT NULL, reason_id INT UNSIGNED NOT NULL, amount INT UNSIGNED NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1FCA0D079B6B5FBA (account_id), UNIQUE INDEX UNIQ_1FCA0D0759BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE withdraw (id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, status SMALLINT NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B5DE5F9EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trade (id INT UNSIGNED NOT NULL, buyer_id INT UNSIGNED NOT NULL, seller_id INT UNSIGNED NOT NULL, lead_id INT UNSIGNED NOT NULL, status SMALLINT UNSIGNED NOT NULL, INDEX IDX_7E1A43666C755722 (buyer_id), INDEX IDX_7E1A43668DE820D9 (seller_id), INDEX IDX_7E1A436655458D (lead_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referrer_reward (id INT UNSIGNED NOT NULL, reason_id INT UNSIGNED NOT NULL, UNIQUE INDEX UNIQ_E96D313A59BB1592 (reason_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `call` (id INT UNSIGNED NOT NULL, caller_id INT UNSIGNED NOT NULL, lead_id INT UNSIGNED NOT NULL, duration_in_secs INT UNSIGNED NOT NULL, INDEX IDX_CC8E2F3EA5626C52 (caller_id), INDEX IDX_CC8E2F3E55458D (lead_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_964964B559BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_964964B5BF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744BF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D59BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hold ADD CONSTRAINT FK_1FCA0D079B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hold ADD CONSTRAINT FK_1FCA0D0759BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE withdraw ADD CONSTRAINT FK_B5DE5F9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE withdraw ADD CONSTRAINT FK_B5DE5F9EBF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43666C755722 FOREIGN KEY (buyer_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A43668DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A436655458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trade ADD CONSTRAINT FK_7E1A4366BF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referrer_reward ADD CONSTRAINT FK_E96D313A59BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE referrer_reward ADD CONSTRAINT FK_E96D313ABF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EA5626C52 FOREIGN KEY (caller_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3E55458D FOREIGN KEY (lead_id) REFERENCES lead (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `call` ADD CONSTRAINT FK_CC8E2F3EBF396750 FOREIGN KEY (id) REFERENCES reason (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fee DROP FOREIGN KEY FK_964964B559BB1592');
        $this->addSql('ALTER TABLE fee DROP FOREIGN KEY FK_964964B5BF396750');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744BF396750');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D59BB1592');
        $this->addSql('ALTER TABLE hold DROP FOREIGN KEY FK_1FCA0D0759BB1592');
        $this->addSql('ALTER TABLE withdraw DROP FOREIGN KEY FK_B5DE5F9EBF396750');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A4366BF396750');
        $this->addSql('ALTER TABLE referrer_reward DROP FOREIGN KEY FK_E96D313A59BB1592');
        $this->addSql('ALTER TABLE referrer_reward DROP FOREIGN KEY FK_E96D313ABF396750');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3EBF396750');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D9B6B5FBA');
        $this->addSql('ALTER TABLE hold DROP FOREIGN KEY FK_1FCA0D079B6B5FBA');
        $this->addSql('ALTER TABLE trade DROP FOREIGN KEY FK_7E1A436655458D');
        $this->addSql('ALTER TABLE `call` DROP FOREIGN KEY FK_CC8E2F3E55458D');
        $this->addSql('DROP TABLE reason');
        $this->addSql('DROP TABLE fee');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE lead');
        $this->addSql('DROP TABLE hold');
        $this->addSql('DROP TABLE withdraw');
        $this->addSql('DROP TABLE trade');
        $this->addSql('DROP TABLE referrer_reward');
        $this->addSql('DROP TABLE `call`');
    }
}

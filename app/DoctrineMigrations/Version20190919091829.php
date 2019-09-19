<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190919091829 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE trades_ask_callback_phone_calls (trade_id INT UNSIGNED NOT NULL, phone_call_id INT UNSIGNED NOT NULL, INDEX IDX_B1B7AA01C2D9760 (trade_id), UNIQUE INDEX UNIQ_B1B7AA01C0EA171E (phone_call_id), PRIMARY KEY(trade_id, phone_call_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trades_ask_callback_phone_calls ADD CONSTRAINT FK_B1B7AA01C2D9760 FOREIGN KEY (trade_id) REFERENCES trade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trades_ask_callback_phone_calls ADD CONSTRAINT FK_B1B7AA01C0EA171E FOREIGN KEY (phone_call_id) REFERENCES phone_call (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE trades_ask_callback_phone_calls');
    }
}

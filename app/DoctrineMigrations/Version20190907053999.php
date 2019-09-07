<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907053999 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO `account` (`balance`, `type`, `description`, `enabled`) VALUES (\'0\', \'income\', \'tincoff-bank\', \'1\');');
        $this->addSql('INSERT INTO `account` (`balance`, `type`, `description`, `enabled`) VALUES (\'0\', \'income\', \'tincoff-bank-uric\', \'1\');');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `account` WHERE `description`=\'tincoff-bank\'');
        $this->addSql('DELETE FROM `account` WHERE `description`=\'tincoff-bank-uric\'');
    }
}

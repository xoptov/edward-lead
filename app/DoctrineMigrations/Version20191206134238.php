<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206134238 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rooms_regions (room_id INT UNSIGNED NOT NULL, region_id INT UNSIGNED NOT NULL, INDEX IDX_120C29FD54177093 (room_id), INDEX IDX_120C29FD98260155 (region_id), PRIMARY KEY(room_id, region_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rooms_cities (room_id INT UNSIGNED NOT NULL, city_id INT UNSIGNED NOT NULL, INDEX IDX_62959D1154177093 (room_id), INDEX IDX_62959D118BAC62AF (city_id), PRIMARY KEY(room_id, city_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_channel (id INT UNSIGNED AUTO_INCREMENT NOT NULL, room_id INT UNSIGNED NOT NULL, property_id INT UNSIGNED NOT NULL, allowed TINYINT(1) NOT NULL, INDEX IDX_4EDB32A754177093 (room_id), INDEX IDX_4EDB32A7549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rooms_regions ADD CONSTRAINT FK_120C29FD54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rooms_regions ADD CONSTRAINT FK_120C29FD98260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rooms_cities ADD CONSTRAINT FK_62959D1154177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rooms_cities ADD CONSTRAINT FK_62959D118BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_channel ADD CONSTRAINT FK_4EDB32A754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_channel ADD CONSTRAINT FK_4EDB32A7549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room ADD public_offer TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rooms_regions');
        $this->addSql('DROP TABLE rooms_cities');
        $this->addSql('DROP TABLE room_channel');
        $this->addSql('ALTER TABLE room DROP public_offer');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240423160111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_equipment (room_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_4F9135EA54177093 (room_id), INDEX IDX_4F9135EA517FE9FE (equipment_id), PRIMARY KEY(room_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE room_equipment ADD CONSTRAINT FK_4F9135EA54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room_equipment ADD CONSTRAINT FK_4F9135EA517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room_equipment DROP FOREIGN KEY FK_4F9135EA54177093');
        $this->addSql('ALTER TABLE room_equipment DROP FOREIGN KEY FK_4F9135EA517FE9FE');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE room_equipment');
    }
}

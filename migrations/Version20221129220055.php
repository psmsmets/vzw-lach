<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129220055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D25505419CB');
        $this->addSql('DROP INDEX UNIQ_CCE5D25505419CB ON associate');
        $this->addSql('ALTER TABLE associate CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE member_details_id associate_details_id INT NOT NULL');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D2544DA14A2 FOREIGN KEY (associate_details_id) REFERENCES associate_details (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CCE5D2544DA14A2 ON associate (associate_details_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D2544DA14A2');
        $this->addSql('DROP INDEX UNIQ_CCE5D2544DA14A2 ON associate');
        $this->addSql('ALTER TABLE associate CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE associate_details_id member_details_id INT NOT NULL');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D25505419CB FOREIGN KEY (member_details_id) REFERENCES associate_details (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CCE5D25505419CB ON associate (member_details_id)');
    }
}

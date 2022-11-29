<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129155318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE associate (id INT AUTO_INCREMENT NOT NULL, member_details_id INT NOT NULL, user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', measurements_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, enabled TINYINT(1) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, singer TINYINT(1) NOT NULL, singer_solo TINYINT(1) NOT NULL, companion VARCHAR(255) NOT NULL, declare_present TINYINT(1) NOT NULL, declare_secrecy TINYINT(1) NOT NULL, declare_risks TINYINT(1) NOT NULL, role VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_CCE5D25505419CB (member_details_id), INDEX IDX_CCE5D25A76ED395 (user_id), UNIQUE INDEX UNIQ_CCE5D25C6570731 (measurements_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE associate_details (id INT AUTO_INCREMENT NOT NULL, secondary_email VARCHAR(255) DEFAULT NULL, secondary_phone VARCHAR(15) DEFAULT NULL, birthdate DATE DEFAULT NULL COMMENT \'(DC2Type:date_immutable)\', gender VARCHAR(1) NOT NULL, address_nation VARCHAR(3) DEFAULT NULL, address_street VARCHAR(255) DEFAULT NULL, address_number VARCHAR(10) DEFAULT NULL, address_zip VARCHAR(12) DEFAULT NULL, address_town VARCHAR(40) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE associate_measurements (id INT AUTO_INCREMENT NOT NULL, shoe_size SMALLINT DEFAULT NULL, head_girth SMALLINT DEFAULT NULL, size VARCHAR(10) DEFAULT NULL, hair_type VARCHAR(40) DEFAULT NULL, hair_color VARCHAR(40) DEFAULT NULL, detail0 SMALLINT DEFAULT NULL, detail1 SMALLINT DEFAULT NULL, detail2 SMALLINT DEFAULT NULL, detail3 SMALLINT DEFAULT NULL, detail4 SMALLINT DEFAULT NULL, detail5 SMALLINT DEFAULT NULL, detail6 SMALLINT DEFAULT NULL, detail7 SMALLINT DEFAULT NULL, detail8 SMALLINT DEFAULT NULL, detail9 SMALLINT DEFAULT NULL, detail10 SMALLINT DEFAULT NULL, detail11 SMALLINT DEFAULT NULL, detail12 SMALLINT DEFAULT NULL, detail13 SMALLINT DEFAULT NULL, detail14 SMALLINT DEFAULT NULL, detail15 SMALLINT DEFAULT NULL, detail16 SMALLINT DEFAULT NULL, detail17 SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, associate_id INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, body LONGTEXT DEFAULT NULL, is_hidden TINYINT(1) NOT NULL, is_actor TINYINT(1) NOT NULL, INDEX IDX_64C19C12B0E8D99 (associate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, mobile_phone VARCHAR(15) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D25505419CB FOREIGN KEY (member_details_id) REFERENCES associate_details (id)');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE associate ADD CONSTRAINT FK_CCE5D25C6570731 FOREIGN KEY (measurements_id) REFERENCES associate_measurements (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C12B0E8D99 FOREIGN KEY (associate_id) REFERENCES associate (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D25505419CB');
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D25A76ED395');
        $this->addSql('ALTER TABLE associate DROP FOREIGN KEY FK_CCE5D25C6570731');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C12B0E8D99');
        $this->addSql('DROP TABLE associate');
        $this->addSql('DROP TABLE associate_details');
        $this->addSql('DROP TABLE associate_measurements');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221216121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (
            id INT AUTO_INCREMENT NOT NULL,
            country VARCHAR(3) NOT NULL,
            city VARCHAR(255) NOT NULL,
            street VARCHAR(255) DEFAULT NULL,
            zip VARCHAR(20) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (
            id INT AUTO_INCREMENT NOT NULL,
            address_id INT DEFAULT NULL,
            category_id INT DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            firstname VARCHAR(255) NOT NULL,
            lastname VARCHAR(255) NOT NULL,
            birthdate DATE NOT NULL,
            INDEX IDX_4C62E638F5B7AF75 (address_id),
            INDEX IDX_4C62E63812469DE2 (category_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact_category (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email (
            id INT AUTO_INCREMENT NOT NULL,
            contact_id INT NOT NULL,
            type VARCHAR(10) NOT NULL,
            email VARCHAR(255) NOT NULL,
            UNIQUE INDEX UNIQ_E7927C74E7927C74 (email),
            INDEX IDX_E7927C74E7A1254A (contact_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (
            id INT AUTO_INCREMENT NOT NULL,
            contact_id INT NOT NULL,
            type VARCHAR(10) NOT NULL,
            number VARCHAR(30) NOT NULL,
            UNIQUE INDEX UNIQ_444F97DD96901F54 (number),
            INDEX IDX_444F97DDE7A1254A (contact_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (
            id INT AUTO_INCREMENT NOT NULL,
            contact_id INT NOT NULL,
            email_id INT NOT NULL,
            UNIQUE INDEX UNIQ_8D93D649E7A1254A (contact_id),
            UNIQUE INDEX UNIQ_8D93D649A832C1C9 (email_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638F5B7AF75
            FOREIGN KEY (address_id) REFERENCES address (id)
        ');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E63812469DE2
            FOREIGN KEY (category_id) REFERENCES contact_category (id)
        ');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74E7A1254A
            FOREIGN KEY (contact_id) REFERENCES contact (id)
        ');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDE7A1254A
            FOREIGN KEY (contact_id) REFERENCES contact (id)
        ');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E7A1254A
            FOREIGN KEY (contact_id) REFERENCES contact (id)
        ');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A832C1C9
            FOREIGN KEY (email_id) REFERENCES email (id)
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638F5B7AF75');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E63812469DE2');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74E7A1254A');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDE7A1254A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E7A1254A');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE contact_category');
        $this->addSql('DROP TABLE email');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP TABLE user');
    }
}

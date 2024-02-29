<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229141510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'We create the user table using SINGLE_TABLE inheritance between user classes and we create ADMIN user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', manager_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_8D93D649783E3463 (manager_id), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649783E3463 FOREIGN KEY (manager_id) REFERENCES user (id)');

        $this->addSql('INSERT INTO user (id, password, email, roles, type) VALUES (UNHEX(REPLACE(UUID(),\'-\',\'\')), \'$2y$13$FFqTyPIXj5leDK3MExTU6eHtIoRM8jiYIC04nVLYQ3pQXwVT.sMM2\', \'admin@example.com\', \'["ROLE_ADMIN"]\', \'admin\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649783E3463');
        $this->addSql('DROP TABLE user');
    }
}

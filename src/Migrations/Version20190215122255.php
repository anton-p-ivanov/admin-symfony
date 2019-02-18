<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215122255 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE workflow (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', status_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_65C59816DE12AB56 (created_by), INDEX IDX_65C5981616FE72E1 (updated_by), INDEX IDX_65C59816E979FD32 (status_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_default TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_B63E2EC777153098 (code), UNIQUE INDEX UNIQ_B63E2EC794E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_passwords (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', password CHAR(60) NOT NULL, salt CHAR(20) NOT NULL, created_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, is_expired TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8BFF51F7ABFE1C6F (user_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_checkwords (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', checkword CHAR(60) NOT NULL, created_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, is_expired TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_1EB3B582DB50E026 (checkword), INDEX IDX_1EB3B582ABFE1C6F (user_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) NOT NULL, fname VARCHAR(100) NOT NULL, lname VARCHAR(100) NOT NULL, sname VARCHAR(100) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, is_confirmed TINYINT(1) DEFAULT \'0\' NOT NULL, phone VARCHAR(255) DEFAULT NULL, phone_mobile VARCHAR(255) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, comments LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E994E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', role_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_51498A8EABFE1C6F (user_uuid), INDEX IDX_51498A8E6FC02232 (role_uuid), PRIMARY KEY(user_uuid, role_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_sites (user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', site_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_5B770E2AABFE1C6F (user_uuid), INDEX IDX_5B770E2AEAE5ED5F (site_uuid), PRIMARY KEY(user_uuid, site_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', is_active TINYINT(1) DEFAULT \'1\' NOT NULL, code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, sort INT DEFAULT 100 NOT NULL, UNIQUE INDEX UNIQ_BC00AA6377153098 (code), UNIQUE INDEX UNIQ_BC00AA6394E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workflow_statuses (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, sort INT DEFAULT 100 NOT NULL, is_default TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_8093E10B77153098 (code), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816DE12AB56 FOREIGN KEY (created_by) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C5981616FE72E1 FOREIGN KEY (updated_by) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816E979FD32 FOREIGN KEY (status_uuid) REFERENCES workflow_statuses (uuid)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC794E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE users_passwords ADD CONSTRAINT FK_8BFF51F7ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_checkwords ADD CONSTRAINT FK_1EB3B582ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E994E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8E6FC02232 FOREIGN KEY (role_uuid) REFERENCES roles (uuid)');
        $this->addSql('ALTER TABLE users_sites ADD CONSTRAINT FK_5B770E2AABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_sites ADD CONSTRAINT FK_5B770E2AEAE5ED5F FOREIGN KEY (site_uuid) REFERENCES sites (uuid)');
        $this->addSql('ALTER TABLE sites ADD CONSTRAINT FK_BC00AA6394E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC794E0409D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E994E0409D');
        $this->addSql('ALTER TABLE sites DROP FOREIGN KEY FK_BC00AA6394E0409D');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8E6FC02232');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816DE12AB56');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C5981616FE72E1');
        $this->addSql('ALTER TABLE users_passwords DROP FOREIGN KEY FK_8BFF51F7ABFE1C6F');
        $this->addSql('ALTER TABLE users_checkwords DROP FOREIGN KEY FK_1EB3B582ABFE1C6F');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EABFE1C6F');
        $this->addSql('ALTER TABLE users_sites DROP FOREIGN KEY FK_5B770E2AABFE1C6F');
        $this->addSql('ALTER TABLE users_sites DROP FOREIGN KEY FK_5B770E2AEAE5ED5F');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816E979FD32');
        $this->addSql('DROP TABLE workflow');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users_passwords');
        $this->addSql('DROP TABLE users_checkwords');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE users_sites');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE workflow_statuses');
    }
}

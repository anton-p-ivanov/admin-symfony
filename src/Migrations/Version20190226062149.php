<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190226062149 extends AbstractMigration
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
        $this->addSql('CREATE TABLE roles (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description MEDIUMTEXT DEFAULT NULL, is_default TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_B63E2EC777153098 (code), UNIQUE INDEX UNIQ_B63E2EC794E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_categories (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description MEDIUMTEXT DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, sort INT DEFAULT 100 NOT NULL, UNIQUE INDEX UNIQ_AAA440E694E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_versions (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', file_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', is_active TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_BA177C95588338C8 (file_uuid), INDEX IDX_BA177C95D8529BB3 (storage_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_requests (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', user_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', status CHAR(1) NOT NULL, created_at DATETIME NOT NULL, expired_at DATETIME DEFAULT NULL, INDEX IDX_D84EEA16D8529BB3 (storage_uuid), INDEX IDX_D84EEA16ABFE1C6F (user_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_tree (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', root_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', parent_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', left_margin INT NOT NULL, right_margin INT NOT NULL, level INT NOT NULL, UNIQUE INDEX UNIQ_BE1395C9D8529BB3 (storage_uuid), INDEX IDX_BE1395C996A2E991 (root_uuid), INDEX IDX_BE1395C9EC9C6612 (parent_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_images (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', file_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', width INT NOT NULL, height INT NOT NULL, UNIQUE INDEX UNIQ_DB1C4A6B588338C8 (file_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', type CHAR(1) NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, content TEXT DEFAULT NULL, UNIQUE INDEX UNIQ_547A1B3494E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_categories_pivot (storage_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', category_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_989F635AD8529BB3 (storage_uuid), INDEX IDX_989F635A5AE42AE1 (category_uuid), PRIMARY KEY(storage_uuid, category_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_files (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', name VARCHAR(255) NOT NULL, size INT NOT NULL, type VARCHAR(255) NOT NULL, hash CHAR(32) NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_passwords (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', password CHAR(60) NOT NULL, salt CHAR(20) NOT NULL, created_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, is_expired TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8BFF51F7ABFE1C6F (user_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_checkwords (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', checkword CHAR(60) NOT NULL, created_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, is_expired TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_1EB3B582DB50E026 (checkword), INDEX IDX_1EB3B582ABFE1C6F (user_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', email VARCHAR(255) NOT NULL, fname VARCHAR(100) NOT NULL, lname VARCHAR(100) NOT NULL, sname VARCHAR(100) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, is_confirmed TINYINT(1) DEFAULT \'0\' NOT NULL, phone VARCHAR(255) DEFAULT NULL, phone_mobile VARCHAR(255) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, comments MEDIUMTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E994E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', role_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_51498A8EABFE1C6F (user_uuid), INDEX IDX_51498A8E6FC02232 (role_uuid), PRIMARY KEY(user_uuid, role_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_sites (user_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', site_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_5B770E2AABFE1C6F (user_uuid), INDEX IDX_5B770E2AEAE5ED5F (site_uuid), PRIMARY KEY(user_uuid, site_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', is_active TINYINT(1) DEFAULT \'1\' NOT NULL, code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, sort INT DEFAULT 100 NOT NULL, UNIQUE INDEX UNIQ_BC00AA6377153098 (code), UNIQUE INDEX UNIQ_BC00AA6394E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_types (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description MEDIUMTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_6DF32B5F77153098 (code), UNIQUE INDEX UNIQ_6DF32B5F94E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_templates (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, sender VARCHAR(255) DEFAULT NULL, recipient VARCHAR(255) NOT NULL, reply_to VARCHAR(255) DEFAULT NULL, copy_to VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, subject VARCHAR(255) NOT NULL, text_body MEDIUMTEXT DEFAULT NULL, html_body MEDIUMTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_17F263ED77153098 (code), INDEX IDX_17F263ED67095AE (type_uuid), UNIQUE INDEX UNIQ_17F263ED94E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_templates_sites (template_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', site_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_777628E74B17ACB (template_uuid), INDEX IDX_777628E7EAE5ED5F (site_uuid), PRIMARY KEY(template_uuid, site_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workflow_statuses (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, sort INT DEFAULT 100 NOT NULL, is_default TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_8093E10B77153098 (code), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816DE12AB56 FOREIGN KEY (created_by) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C5981616FE72E1 FOREIGN KEY (updated_by) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE workflow ADD CONSTRAINT FK_65C59816E979FD32 FOREIGN KEY (status_uuid) REFERENCES workflow_statuses (uuid)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC794E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE storage_categories ADD CONSTRAINT FK_AAA440E694E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE storage_versions ADD CONSTRAINT FK_BA177C95588338C8 FOREIGN KEY (file_uuid) REFERENCES storage_files (uuid)');
        $this->addSql('ALTER TABLE storage_versions ADD CONSTRAINT FK_BA177C95D8529BB3 FOREIGN KEY (storage_uuid) REFERENCES storage (uuid)');
        $this->addSql('ALTER TABLE storage_requests ADD CONSTRAINT FK_D84EEA16D8529BB3 FOREIGN KEY (storage_uuid) REFERENCES storage (uuid)');
        $this->addSql('ALTER TABLE storage_requests ADD CONSTRAINT FK_D84EEA16ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE storage_tree ADD CONSTRAINT FK_BE1395C9D8529BB3 FOREIGN KEY (storage_uuid) REFERENCES storage (uuid)');
        $this->addSql('ALTER TABLE storage_tree ADD CONSTRAINT FK_BE1395C996A2E991 FOREIGN KEY (root_uuid) REFERENCES storage_tree (uuid)');
        $this->addSql('ALTER TABLE storage_tree ADD CONSTRAINT FK_BE1395C9EC9C6612 FOREIGN KEY (parent_uuid) REFERENCES storage_tree (uuid)');
        $this->addSql('ALTER TABLE storage_images ADD CONSTRAINT FK_DB1C4A6B588338C8 FOREIGN KEY (file_uuid) REFERENCES storage_files (uuid)');
        $this->addSql('ALTER TABLE storage ADD CONSTRAINT FK_547A1B3494E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE storage_categories_pivot ADD CONSTRAINT FK_989F635AD8529BB3 FOREIGN KEY (storage_uuid) REFERENCES storage (uuid)');
        $this->addSql('ALTER TABLE storage_categories_pivot ADD CONSTRAINT FK_989F635A5AE42AE1 FOREIGN KEY (category_uuid) REFERENCES storage_categories (uuid)');
        $this->addSql('ALTER TABLE users_passwords ADD CONSTRAINT FK_8BFF51F7ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_checkwords ADD CONSTRAINT FK_1EB3B582ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E994E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8E6FC02232 FOREIGN KEY (role_uuid) REFERENCES roles (uuid)');
        $this->addSql('ALTER TABLE users_sites ADD CONSTRAINT FK_5B770E2AABFE1C6F FOREIGN KEY (user_uuid) REFERENCES users (uuid)');
        $this->addSql('ALTER TABLE users_sites ADD CONSTRAINT FK_5B770E2AEAE5ED5F FOREIGN KEY (site_uuid) REFERENCES sites (uuid)');
        $this->addSql('ALTER TABLE sites ADD CONSTRAINT FK_BC00AA6394E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE mail_types ADD CONSTRAINT FK_6DF32B5F94E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE mail_templates ADD CONSTRAINT FK_17F263ED67095AE FOREIGN KEY (type_uuid) REFERENCES mail_types (uuid)');
        $this->addSql('ALTER TABLE mail_templates ADD CONSTRAINT FK_17F263ED94E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE mail_templates_sites ADD CONSTRAINT FK_777628E74B17ACB FOREIGN KEY (template_uuid) REFERENCES mail_templates (uuid)');
        $this->addSql('ALTER TABLE mail_templates_sites ADD CONSTRAINT FK_777628E7EAE5ED5F FOREIGN KEY (site_uuid) REFERENCES sites (uuid)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC794E0409D');
        $this->addSql('ALTER TABLE storage_categories DROP FOREIGN KEY FK_AAA440E694E0409D');
        $this->addSql('ALTER TABLE storage DROP FOREIGN KEY FK_547A1B3494E0409D');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E994E0409D');
        $this->addSql('ALTER TABLE sites DROP FOREIGN KEY FK_BC00AA6394E0409D');
        $this->addSql('ALTER TABLE mail_types DROP FOREIGN KEY FK_6DF32B5F94E0409D');
        $this->addSql('ALTER TABLE mail_templates DROP FOREIGN KEY FK_17F263ED94E0409D');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8E6FC02232');
        $this->addSql('ALTER TABLE storage_categories_pivot DROP FOREIGN KEY FK_989F635A5AE42AE1');
        $this->addSql('ALTER TABLE storage_tree DROP FOREIGN KEY FK_BE1395C996A2E991');
        $this->addSql('ALTER TABLE storage_tree DROP FOREIGN KEY FK_BE1395C9EC9C6612');
        $this->addSql('ALTER TABLE storage_versions DROP FOREIGN KEY FK_BA177C95D8529BB3');
        $this->addSql('ALTER TABLE storage_requests DROP FOREIGN KEY FK_D84EEA16D8529BB3');
        $this->addSql('ALTER TABLE storage_tree DROP FOREIGN KEY FK_BE1395C9D8529BB3');
        $this->addSql('ALTER TABLE storage_categories_pivot DROP FOREIGN KEY FK_989F635AD8529BB3');
        $this->addSql('ALTER TABLE storage_versions DROP FOREIGN KEY FK_BA177C95588338C8');
        $this->addSql('ALTER TABLE storage_images DROP FOREIGN KEY FK_DB1C4A6B588338C8');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816DE12AB56');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C5981616FE72E1');
        $this->addSql('ALTER TABLE storage_requests DROP FOREIGN KEY FK_D84EEA16ABFE1C6F');
        $this->addSql('ALTER TABLE users_passwords DROP FOREIGN KEY FK_8BFF51F7ABFE1C6F');
        $this->addSql('ALTER TABLE users_checkwords DROP FOREIGN KEY FK_1EB3B582ABFE1C6F');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EABFE1C6F');
        $this->addSql('ALTER TABLE users_sites DROP FOREIGN KEY FK_5B770E2AABFE1C6F');
        $this->addSql('ALTER TABLE users_sites DROP FOREIGN KEY FK_5B770E2AEAE5ED5F');
        $this->addSql('ALTER TABLE mail_templates_sites DROP FOREIGN KEY FK_777628E7EAE5ED5F');
        $this->addSql('ALTER TABLE mail_templates DROP FOREIGN KEY FK_17F263ED67095AE');
        $this->addSql('ALTER TABLE mail_templates_sites DROP FOREIGN KEY FK_777628E74B17ACB');
        $this->addSql('ALTER TABLE workflow DROP FOREIGN KEY FK_65C59816E979FD32');
        $this->addSql('DROP TABLE workflow');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE storage_categories');
        $this->addSql('DROP TABLE storage_versions');
        $this->addSql('DROP TABLE storage_requests');
        $this->addSql('DROP TABLE storage_tree');
        $this->addSql('DROP TABLE storage_images');
        $this->addSql('DROP TABLE storage');
        $this->addSql('DROP TABLE storage_categories_pivot');
        $this->addSql('DROP TABLE storage_files');
        $this->addSql('DROP TABLE users_passwords');
        $this->addSql('DROP TABLE users_checkwords');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE users_sites');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE mail_types');
        $this->addSql('DROP TABLE mail_templates');
        $this->addSql('DROP TABLE mail_templates_sites');
        $this->addSql('DROP TABLE workflow_statuses');
    }
}

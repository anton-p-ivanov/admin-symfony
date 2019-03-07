<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190226122232 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE storage_roles (storage_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', role_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_DBEAE7E7D8529BB3 (storage_uuid), INDEX IDX_DBEAE7E76FC02232 (role_uuid), PRIMARY KEY(storage_uuid, role_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE storage_roles ADD CONSTRAINT FK_DBEAE7E7D8529BB3 FOREIGN KEY (storage_uuid) REFERENCES storage (uuid)');
        $this->addSql('ALTER TABLE storage_roles ADD CONSTRAINT FK_DBEAE7E76FC02232 FOREIGN KEY (role_uuid) REFERENCES roles (uuid)');
        $this->addSql('ALTER TABLE workflow CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE status_uuid status_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_categories CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_versions CHANGE file_uuid file_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_requests CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE user_uuid user_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE expired_at expired_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE storage_tree CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE root_uuid root_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE parent_uuid parent_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_images CHANGE file_uuid file_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE users_passwords CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE expired_at expired_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users_checkwords CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE expired_at expired_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE sname sname VARCHAR(100) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE phone_mobile phone_mobile VARCHAR(255) DEFAULT NULL, CHANGE skype skype VARCHAR(255) DEFAULT NULL, CHANGE birthdate birthdate DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE sites CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE mail_types CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE mail_templates CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE sender sender VARCHAR(255) DEFAULT NULL, CHANGE reply_to reply_to VARCHAR(255) DEFAULT NULL, CHANGE copy_to copy_to VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE storage_roles');
        $this->addSql('ALTER TABLE mail_templates CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE sender sender VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE reply_to reply_to VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE copy_to copy_to VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE mail_types CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE roles CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE sites CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_categories CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_images CHANGE file_uuid file_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_requests CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE user_uuid user_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE expired_at expired_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE storage_tree CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE root_uuid root_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE parent_uuid parent_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE storage_versions CHANGE file_uuid file_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE storage_uuid storage_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE users CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE sname sname VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone_mobile phone_mobile VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE skype skype VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE birthdate birthdate DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users_checkwords CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE expired_at expired_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users_passwords CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE expired_at expired_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE workflow CHANGE created_by created_by CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE status_uuid status_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
    }
}

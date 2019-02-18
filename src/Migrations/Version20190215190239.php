<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190215190239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mail_types (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_6DF32B5F77153098 (code), UNIQUE INDEX UNIQ_6DF32B5F94E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_templates (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', type_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', code VARCHAR(255) NOT NULL, sender VARCHAR(255) DEFAULT NULL, recipient VARCHAR(255) NOT NULL, reply_to VARCHAR(255) DEFAULT NULL, copy_to VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT \'1\' NOT NULL, subject VARCHAR(255) NOT NULL, text_body LONGTEXT DEFAULT NULL, html_body LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_17F263ED77153098 (code), INDEX IDX_17F263ED67095AE (type_uuid), UNIQUE INDEX UNIQ_17F263ED94E0409D (workflow_uuid), PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_templates_sites (template_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', site_uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_777628E74B17ACB (template_uuid), INDEX IDX_777628E7EAE5ED5F (site_uuid), PRIMARY KEY(template_uuid, site_uuid)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mail_types ADD CONSTRAINT FK_6DF32B5F94E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE mail_templates ADD CONSTRAINT FK_17F263ED67095AE FOREIGN KEY (type_uuid) REFERENCES mail_types (uuid)');
        $this->addSql('ALTER TABLE mail_templates ADD CONSTRAINT FK_17F263ED94E0409D FOREIGN KEY (workflow_uuid) REFERENCES workflow (uuid)');
        $this->addSql('ALTER TABLE mail_templates_sites ADD CONSTRAINT FK_777628E74B17ACB FOREIGN KEY (template_uuid) REFERENCES mail_templates (uuid)');
        $this->addSql('ALTER TABLE mail_templates_sites ADD CONSTRAINT FK_777628E7EAE5ED5F FOREIGN KEY (site_uuid) REFERENCES sites (uuid)');
        $this->addSql('ALTER TABLE workflow CHANGE created_by created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE status_uuid status_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE users_passwords CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE expired_at expired_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users_checkwords CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE expired_at expired_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', CHANGE sname sname VARCHAR(100) DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL, CHANGE phone_mobile phone_mobile VARCHAR(255) DEFAULT NULL, CHANGE skype skype VARCHAR(255) DEFAULT NULL, CHANGE birthdate birthdate DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE sites CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE mail_templates DROP FOREIGN KEY FK_17F263ED67095AE');
        $this->addSql('ALTER TABLE mail_templates_sites DROP FOREIGN KEY FK_777628E74B17ACB');
        $this->addSql('DROP TABLE mail_types');
        $this->addSql('DROP TABLE mail_templates');
        $this->addSql('DROP TABLE mail_templates_sites');
        $this->addSql('ALTER TABLE roles CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE sites CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE users CHANGE workflow_uuid workflow_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE sname sname VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone_mobile phone_mobile VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE skype skype VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE birthdate birthdate DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users_checkwords CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE expired_at expired_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE users_passwords CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE expired_at expired_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE workflow CHANGE created_by created_by CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE updated_by updated_by CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE status_uuid status_uuid CHAR(36) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:guid)\', CHANGE created_at created_at DATETIME DEFAULT \'NULL\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\'');
    }
}

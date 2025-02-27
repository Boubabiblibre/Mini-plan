<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226123417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, color VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_64C19C1A76ED395 ON category (user_id)');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN category.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE member (id UUID NOT NULL, space_id UUID NOT NULL, user_id UUID NOT NULL, name VARCHAR(255) NOT NULL, relationship VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_70E4FA7823575340 ON member (space_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78A76ED395 ON member (user_id)');
        $this->addSql('COMMENT ON COLUMN member.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN member.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN member.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN member.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN member.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notification (id UUID NOT NULL, sender_id UUID DEFAULT NULL, space_id UUID DEFAULT NULL, notification_type VARCHAR(255) NOT NULL, message TEXT NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF5476CAF624B39D ON notification (sender_id)');
        $this->addSql('CREATE INDEX IDX_BF5476CA23575340 ON notification (space_id)');
        $this->addSql('COMMENT ON COLUMN notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification.sent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE notification_target (id UUID NOT NULL, notification_id UUID NOT NULL, user_id UUID NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_82C8D1A1EF1A9D84 ON notification_target (notification_id)');
        $this->addSql('CREATE INDEX IDX_82C8D1A1A76ED395 ON notification_target (user_id)');
        $this->addSql('COMMENT ON COLUMN notification_target.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification_target.notification_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification_target.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification_target.read_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification_target.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN notification_target.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE payment (id UUID NOT NULL, subscription_id UUID NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D9A1887DC ON payment (subscription_id)');
        $this->addSql('COMMENT ON COLUMN payment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN payment.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE permission (id UUID NOT NULL, user_id UUID NOT NULL, space_id UUID NOT NULL, permission_type VARCHAR(255) NOT NULL, assigned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E04992AAA76ED395 ON permission (user_id)');
        $this->addSql('CREATE INDEX IDX_E04992AA23575340 ON permission (space_id)');
        $this->addSql('COMMENT ON COLUMN permission.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN permission.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN permission.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN permission.assigned_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE service (id UUID NOT NULL, category_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, provider VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD212469DE2 ON service (category_id)');
        $this->addSql('COMMENT ON COLUMN service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN service.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE space (id UUID NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN space.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN space.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN space.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subscription (id UUID NOT NULL, member_id UUID NOT NULL, service_id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, notes TEXT DEFAULT NULL, subscription_type VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, amount NUMERIC(10, 2) DEFAULT NULL, total_paid NUMERIC(10, 2) DEFAULT NULL, auto_renewal BOOLEAN DEFAULT NULL, billing_mode VARCHAR(255) NOT NULL, billing_frequency VARCHAR(255) DEFAULT NULL, billing_day DATE DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D37597D3FE ON subscription (member_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3ED5CA9E6 ON subscription (service_id)');
        $this->addSql('COMMENT ON COLUMN subscription.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.member_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subscription_tag (id UUID NOT NULL, subscription_id UUID NOT NULL, tag_id UUID NOT NULL, read_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E7A259689A1887DC ON subscription_tag (subscription_id)');
        $this->addSql('CREATE INDEX IDX_E7A25968BAD26311 ON subscription_tag (tag_id)');
        $this->addSql('COMMENT ON COLUMN subscription_tag.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tag.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tag.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tag.read_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tag.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tag.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tag (id UUID NOT NULL, user_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_389B783A76ED395 ON tag (user_id)');
        $this->addSql('COMMENT ON COLUMN tag.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tag.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tag.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tag.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE users (id UUID NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, age INT DEFAULT NULL, phone_number VARCHAR(50) DEFAULT NULL, password TEXT NOT NULL, is_active BOOLEAN DEFAULT false NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN users.last_login IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN users.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA7823575340 FOREIGN KEY (space_id) REFERENCES space (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA23575340 FOREIGN KEY (space_id) REFERENCES space (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification_target ADD CONSTRAINT FK_82C8D1A1EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notification_target ADD CONSTRAINT FK_82C8D1A1A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA23575340 FOREIGN KEY (space_id) REFERENCES space (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D37597D3FE FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_tag ADD CONSTRAINT FK_E7A259689A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_tag ADD CONSTRAINT FK_E7A25968BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1A76ED395');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA7823575340');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA78A76ED395');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CAF624B39D');
        $this->addSql('ALTER TABLE notification DROP CONSTRAINT FK_BF5476CA23575340');
        $this->addSql('ALTER TABLE notification_target DROP CONSTRAINT FK_82C8D1A1EF1A9D84');
        $this->addSql('ALTER TABLE notification_target DROP CONSTRAINT FK_82C8D1A1A76ED395');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D9A1887DC');
        $this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AAA76ED395');
        $this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA23575340');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD212469DE2');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D37597D3FE');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3ED5CA9E6');
        $this->addSql('ALTER TABLE subscription_tag DROP CONSTRAINT FK_E7A259689A1887DC');
        $this->addSql('ALTER TABLE subscription_tag DROP CONSTRAINT FK_E7A25968BAD26311');
        $this->addSql('ALTER TABLE tag DROP CONSTRAINT FK_389B783A76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notification_target');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE space');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250823175702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invitation (id SERIAL NOT NULL, space_id UUID NOT NULL, invited_by_id UUID DEFAULT NULL, email VARCHAR(180) NOT NULL, relationship VARCHAR(24) NOT NULL, date_of_birth TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, token VARCHAR(96) NOT NULL, status VARCHAR(16) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F11D61A25F37A13B ON invitation (token)');
        $this->addSql('CREATE INDEX IDX_F11D61A223575340 ON invitation (space_id)');
        $this->addSql('CREATE INDEX IDX_F11D61A2A7B4A7E3 ON invitation (invited_by_id)');
        $this->addSql('COMMENT ON COLUMN invitation.date_of_birth IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN invitation.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A223575340 FOREIGN KEY (space_id) REFERENCES spaces (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invitation ADD CONSTRAINT FK_F11D61A2A7B4A7E3 FOREIGN KEY (invited_by_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categories ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE categories ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN categories.id IS NULL');
        $this->addSql('COMMENT ON COLUMN categories.user_id IS NULL');
        $this->addSql('ALTER TABLE members ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE members ALTER space_id TYPE UUID');
        $this->addSql('ALTER TABLE members ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN members.id IS NULL');
        $this->addSql('COMMENT ON COLUMN members.space_id IS NULL');
        $this->addSql('COMMENT ON COLUMN members.user_id IS NULL');
        $this->addSql('ALTER TABLE notification_targets ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE notification_targets ALTER notification_id TYPE UUID');
        $this->addSql('ALTER TABLE notification_targets ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN notification_targets.id IS NULL');
        $this->addSql('COMMENT ON COLUMN notification_targets.notification_id IS NULL');
        $this->addSql('COMMENT ON COLUMN notification_targets.user_id IS NULL');
        $this->addSql('ALTER TABLE notifications ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER sender_id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER space_id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER receiver_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN notifications.id IS NULL');
        $this->addSql('COMMENT ON COLUMN notifications.sender_id IS NULL');
        $this->addSql('COMMENT ON COLUMN notifications.space_id IS NULL');
        $this->addSql('COMMENT ON COLUMN notifications.receiver_id IS NULL');
        $this->addSql('ALTER TABLE payments ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE payments ALTER subscription_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN payments.id IS NULL');
        $this->addSql('COMMENT ON COLUMN payments.subscription_id IS NULL');
        $this->addSql('ALTER TABLE permissions ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE permissions ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE permissions ALTER space_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN permissions.id IS NULL');
        $this->addSql('COMMENT ON COLUMN permissions.user_id IS NULL');
        $this->addSql('COMMENT ON COLUMN permissions.space_id IS NULL');
        $this->addSql('ALTER TABLE services ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE services ALTER category_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN services.id IS NULL');
        $this->addSql('COMMENT ON COLUMN services.category_id IS NULL');
        $this->addSql('ALTER TABLE spaces ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE spaces ALTER created_by_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN spaces.id IS NULL');
        $this->addSql('COMMENT ON COLUMN spaces.created_by_id IS NULL');
        $this->addSql('ALTER TABLE subscription_tags ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE subscription_tags ALTER subscription_id TYPE UUID');
        $this->addSql('ALTER TABLE subscription_tags ALTER tag_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN subscription_tags.id IS NULL');
        $this->addSql('COMMENT ON COLUMN subscription_tags.subscription_id IS NULL');
        $this->addSql('COMMENT ON COLUMN subscription_tags.tag_id IS NULL');
        $this->addSql('ALTER TABLE subscriptions ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER member_id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER service_id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN subscriptions.id IS NULL');
        $this->addSql('COMMENT ON COLUMN subscriptions.member_id IS NULL');
        $this->addSql('COMMENT ON COLUMN subscriptions.service_id IS NULL');
        $this->addSql('COMMENT ON COLUMN subscriptions.user_id IS NULL');
        $this->addSql('ALTER TABLE tags ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE tags ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN tags.id IS NULL');
        $this->addSql('COMMENT ON COLUMN tags.user_id IS NULL');
        $this->addSql('ALTER TABLE users ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN users.id IS NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A223575340');
        $this->addSql('ALTER TABLE invitation DROP CONSTRAINT FK_F11D61A2A7B4A7E3');
        $this->addSql('DROP TABLE invitation');
        $this->addSql('ALTER TABLE subscriptions ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER member_id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER service_id TYPE UUID');
        $this->addSql('ALTER TABLE subscriptions ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN subscriptions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriptions.member_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriptions.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscriptions.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE "users" ALTER id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN "users".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_tags ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE subscription_tags ALTER subscription_id TYPE UUID');
        $this->addSql('ALTER TABLE subscription_tags ALTER tag_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN subscription_tags.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tags.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_tags.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE services ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE services ALTER category_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN services.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN services.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payments ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE payments ALTER subscription_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN payments.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payments.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notification_targets ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE notification_targets ALTER notification_id TYPE UUID');
        $this->addSql('ALTER TABLE notification_targets ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN notification_targets.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification_targets.notification_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notification_targets.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE categories ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE categories ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN categories.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN categories.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE permissions ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE permissions ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE permissions ALTER space_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN permissions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN permissions.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN permissions.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE spaces ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE spaces ALTER created_by_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN spaces.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN spaces.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notifications ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER sender_id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER space_id TYPE UUID');
        $this->addSql('ALTER TABLE notifications ALTER receiver_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN notifications.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notifications.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notifications.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notifications.receiver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE members ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE members ALTER space_id TYPE UUID');
        $this->addSql('ALTER TABLE members ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN members.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN members.space_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN members.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tags ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE tags ALTER user_id TYPE UUID');
        $this->addSql('COMMENT ON COLUMN tags.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tags.user_id IS \'(DC2Type:uuid)\'');
    }
}

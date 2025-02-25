<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224160839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter la colonne roles avec une valeur par défaut
        $this->addSql('ALTER TABLE users ADD roles JSON DEFAULT \'[]\' NOT NULL');
    
        // Mettre à jour les utilisateurs existants pour éviter l'erreur de null
        $this->addSql("UPDATE users SET roles = '[]' WHERE roles IS NULL");
    }
    
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users DROP roles');
    }
}

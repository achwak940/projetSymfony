<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325215745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD detail_commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DEDE14305 FOREIGN KEY (detail_commande_id) REFERENCES detail_commande (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DEDE14305 ON commande (detail_commande_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DEDE14305');
        $this->addSql('DROP INDEX IDX_6EEAA67DEDE14305 ON commande');
        $this->addSql('ALTER TABLE commande DROP detail_commande_id');
    }
}

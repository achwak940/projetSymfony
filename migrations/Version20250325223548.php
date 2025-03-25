<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325223548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE99004AB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DE99004AB');
        $this->addSql('DROP TABLE details_commande');
        $this->addSql('DROP INDEX IDX_6EEAA67DE99004AB ON commande');
        $this->addSql('ALTER TABLE commande DROP details_commande_id');
        $this->addSql('DROP INDEX IDX_D34A04ADE99004AB ON product');
        $this->addSql('ALTER TABLE product DROP details_commande_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE details_commande (id INT AUTO_INCREMENT NOT NULL, quantite NUMERIC(10, 0) NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product ADD details_commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE99004AB FOREIGN KEY (details_commande_id) REFERENCES details_commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D34A04ADE99004AB ON product (details_commande_id)');
        $this->addSql('ALTER TABLE commande ADD details_commande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DE99004AB FOREIGN KEY (details_commande_id) REFERENCES details_commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6EEAA67DE99004AB ON commande (details_commande_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190702213453 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articoli (id INT AUTO_INCREMENT NOT NULL, codice VARCHAR(13) NOT NULL, descrizione VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_913BCD02A48A0183 (codice), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, timestamp DATETIME NOT NULL, user VARCHAR(255) NOT NULL, descrizione VARCHAR(255) NOT NULL, ip VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ubicazioni (id INT AUTO_INCREMENT NOT NULL, articolo_id INT DEFAULT NULL, codice VARCHAR(13) NOT NULL, fila INT NOT NULL, colonna VARCHAR(1) NOT NULL, piano INT NOT NULL, INDEX IDX_4EFAEDB08FF10E34 (articolo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ubicazioni ADD CONSTRAINT FK_4EFAEDB08FF10E34 FOREIGN KEY (articolo_id) REFERENCES articoli (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ubicazioni DROP FOREIGN KEY FK_4EFAEDB08FF10E34');
        $this->addSql('DROP TABLE articoli');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE ubicazioni');
    }
}

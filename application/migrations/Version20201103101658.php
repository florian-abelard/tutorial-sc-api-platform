<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103101658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE cheese_notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE cheese_notification (id INT NOT NULL, cheese_listing_id INT NOT NULL, notification_text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D33F5BC5B167220F ON cheese_notification (cheese_listing_id)');
        $this->addSql('ALTER TABLE cheese_notification ADD CONSTRAINT FK_D33F5BC5B167220F FOREIGN KEY (cheese_listing_id) REFERENCES cheese_listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE cheese_notification_id_seq CASCADE');
        $this->addSql('DROP TABLE cheese_notification');
    }
}

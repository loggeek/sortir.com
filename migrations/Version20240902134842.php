<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902134842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE excursion ADD campus_id INT NOT NULL');
        $this->addSql('ALTER TABLE excursion ADD CONSTRAINT FK_9B08E72FAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('CREATE INDEX IDX_9B08E72FAF5D55E1 ON excursion (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE excursion DROP FOREIGN KEY FK_9B08E72FAF5D55E1');
        $this->addSql('DROP INDEX IDX_9B08E72FAF5D55E1 ON excursion');
        $this->addSql('ALTER TABLE excursion DROP campus_id');
    }
}

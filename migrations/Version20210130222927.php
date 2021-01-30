<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210130222927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C4327294869C');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C4327294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C4327294869C');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C4327294869C FOREIGN KEY (article_id) REFERENCES article (id)');
    }
}

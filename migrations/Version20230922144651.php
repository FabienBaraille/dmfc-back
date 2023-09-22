<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230922144651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE srprediction ADD user_id INT NOT NULL, ADD game_id INT NOT NULL');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_5BFC79E7A76ED395 ON srprediction (user_id)');
        $this->addSql('CREATE INDEX IDX_5BFC79E7E48FD905 ON srprediction (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7A76ED395');
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7E48FD905');
        $this->addSql('DROP INDEX IDX_5BFC79E7A76ED395 ON srprediction');
        $this->addSql('DROP INDEX IDX_5BFC79E7E48FD905 ON srprediction');
        $this->addSql('ALTER TABLE srprediction DROP user_id, DROP game_id');
    }
}

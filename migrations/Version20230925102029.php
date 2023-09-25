<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925102029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game ADD round_id INT NOT NULL');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CA6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('CREATE INDEX IDX_232B318CA6005CA0 ON game (round_id)');
        $this->addSql('ALTER TABLE round ADD season_id INT NOT NULL, ADD league_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA344EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA3458AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C5EEEA344EC001D1 ON round (season_id)');
        $this->addSql('CREATE INDEX IDX_C5EEEA3458AFC4DE ON round (league_id)');
        $this->addSql('CREATE INDEX IDX_C5EEEA34A76ED395 ON round (user_id)');
        $this->addSql('ALTER TABLE team ADD game_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE INDEX IDX_C4E0A61FE48FD905 ON team (game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CA6005CA0');
        $this->addSql('DROP INDEX IDX_232B318CA6005CA0 ON game');
        $this->addSql('ALTER TABLE game DROP round_id');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA344EC001D1');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA3458AFC4DE');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34A76ED395');
        $this->addSql('DROP INDEX IDX_C5EEEA344EC001D1 ON round');
        $this->addSql('DROP INDEX IDX_C5EEEA3458AFC4DE ON round');
        $this->addSql('DROP INDEX IDX_C5EEEA34A76ED395 ON round');
        $this->addSql('ALTER TABLE round DROP season_id, DROP league_id, DROP user_id');
        $this->addSql('ALTER TABLE team DROP FOREIGN KEY FK_C4E0A61FE48FD905');
        $this->addSql('DROP INDEX IDX_C4E0A61FE48FD905 ON team');
        $this->addSql('ALTER TABLE team DROP game_id');
    }
}

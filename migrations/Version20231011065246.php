<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011065246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33296CD8AE');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33E48FD905');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F0E45BA9BB827337 ON season (year)');
        $this->addSql('ALTER TABLE team CHANGE nb_selected_home nb_selected_home VARCHAR(3) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61FE9E2A637 ON team (trigram)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4E0A61F5E237E06 ON team (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F0E45BA9BB827337 ON season');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33E48FD905');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33296CD8AE');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_C4E0A61FE9E2A637 ON team');
        $this->addSql('DROP INDEX UNIQ_C4E0A61F5E237E06 ON team');
        $this->addSql('ALTER TABLE team CHANGE nb_selected_home nb_selected_home SMALLINT DEFAULT NULL');
    }
}

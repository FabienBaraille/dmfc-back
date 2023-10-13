<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231012124323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, date_and_time_of_match DATETIME NOT NULL, visitor_score SMALLINT DEFAULT NULL, home_score SMALLINT DEFAULT NULL, winner VARCHAR(60) DEFAULT NULL, visitor_odd DOUBLE PRECISION DEFAULT NULL, home_odd DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_232B318CA6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_team (game_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_2FF5CA33E48FD905 (game_id), INDEX IDX_2FF5CA33296CD8AE (team_id), PRIMARY KEY(game_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leaderboard (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, season_id INT NOT NULL, final_score SMALLINT DEFAULT NULL, final_rank SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_182E5253A76ED395 (user_id), INDEX IDX_182E52534EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league (id INT AUTO_INCREMENT NOT NULL, league_name VARCHAR(180) NOT NULL, league_description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, league_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1DD3995058AFC4DE (league_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, league_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(60) NOT NULL, category VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C5EEEA344EC001D1 (season_id), INDEX IDX_C5EEEA3458AFC4DE (league_id), INDEX IDX_C5EEEA34A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, year VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, start_season DATETIME DEFAULT NULL, start_playoff DATETIME DEFAULT NULL, comment LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_F0E45BA9BB827337 (year), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE srprediction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, predicted_winnig_team VARCHAR(180) NOT NULL, predicted_point_difference VARCHAR(10) NOT NULL, validation_status VARCHAR(20) NOT NULL, point_scored SMALLINT NOT NULL, bonus_points_erned SMALLINT NOT NULL, bonus_bookie SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5BFC79E7A76ED395 (user_id), INDEX IDX_5BFC79E7E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, trigram VARCHAR(3) NOT NULL, name VARCHAR(60) NOT NULL, conference VARCHAR(10) NOT NULL, logo VARCHAR(180) DEFAULT NULL, ranking SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C4E0A61FE9E2A637 (trigram), UNIQUE INDEX UNIQ_C4E0A61F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, league_id INT DEFAULT NULL, username VARCHAR(60) NOT NULL, password VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', title VARCHAR(60) DEFAULT NULL, score SMALLINT DEFAULT NULL, old_position SMALLINT DEFAULT NULL, season_played SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649296CD8AE (team_id), INDEX IDX_8D93D64958AFC4DE (league_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CA6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE leaderboard ADD CONSTRAINT FK_182E5253A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE leaderboard ADD CONSTRAINT FK_182E52534EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD3995058AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA344EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA3458AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64958AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CA6005CA0');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33E48FD905');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33296CD8AE');
        $this->addSql('ALTER TABLE leaderboard DROP FOREIGN KEY FK_182E5253A76ED395');
        $this->addSql('ALTER TABLE leaderboard DROP FOREIGN KEY FK_182E52534EC001D1');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD3995058AFC4DE');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA344EC001D1');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA3458AFC4DE');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34A76ED395');
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7A76ED395');
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7E48FD905');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649296CD8AE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64958AFC4DE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_team');
        $this->addSql('DROP TABLE leaderboard');
        $this->addSql('DROP TABLE league');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE srprediction');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

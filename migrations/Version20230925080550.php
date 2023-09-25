<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925080550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, date_and_time_of_match DATETIME NOT NULL, visitor_score SMALLINT DEFAULT NULL, home_score SMALLINT DEFAULT NULL, winner VARCHAR(60) DEFAULT NULL, visitor_odd DOUBLE PRECISION DEFAULT NULL, home_odd DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leaderboard (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, season_id INT NOT NULL, final_score SMALLINT DEFAULT NULL, final_rank SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_182E5253A76ED395 (user_id), INDEX IDX_182E52534EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league (id INT AUTO_INCREMENT NOT NULL, league_name VARCHAR(180) NOT NULL, league_description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, category VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, year VARCHAR(60) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE srprediction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, predicted_winnig_team VARCHAR(180) NOT NULL, predicted_point_difference VARCHAR(10) NOT NULL, validation_status VARCHAR(20) NOT NULL, point_scored SMALLINT NOT NULL, bonus_points_erned SMALLINT NOT NULL, bonus_bookie SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5BFC79E7A76ED395 (user_id), INDEX IDX_5BFC79E7E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, trigram VARCHAR(3) NOT NULL, name VARCHAR(60) NOT NULL, conference VARCHAR(10) NOT NULL, logo VARCHAR(180) DEFAULT NULL, nb_selected_home SMALLINT DEFAULT NULL, nb_selected_away SMALLINT DEFAULT NULL, ranking SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(60) NOT NULL, password VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, role LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', title VARCHAR(60) DEFAULT NULL, score SMALLINT DEFAULT NULL, old_position SMALLINT DEFAULT NULL, position SMALLINT DEFAULT NULL, season_played SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leaderboard ADD CONSTRAINT FK_182E5253A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE leaderboard ADD CONSTRAINT FK_182E52534EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE srprediction ADD CONSTRAINT FK_5BFC79E7E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leaderboard DROP FOREIGN KEY FK_182E5253A76ED395');
        $this->addSql('ALTER TABLE leaderboard DROP FOREIGN KEY FK_182E52534EC001D1');
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7A76ED395');
        $this->addSql('ALTER TABLE srprediction DROP FOREIGN KEY FK_5BFC79E7E48FD905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE leaderboard');
        $this->addSql('DROP TABLE league');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE srprediction');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

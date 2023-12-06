<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202211917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE top_ten (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, conference VARCHAR(11) NOT NULL, deadline DATETIME NOT NULL, results LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_F72D7AC3A6005CA0 (round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE top_ten_team (top_ten_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_589AA63E65A720D7 (top_ten_id), INDEX IDX_589AA63E296CD8AE (team_id), PRIMARY KEY(top_ten_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE top_ten ADD CONSTRAINT FK_F72D7AC3A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE top_ten_team ADD CONSTRAINT FK_589AA63E65A720D7 FOREIGN KEY (top_ten_id) REFERENCES top_ten (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE top_ten_team ADD CONSTRAINT FK_589AA63E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE top_ten DROP FOREIGN KEY FK_F72D7AC3A6005CA0');
        $this->addSql('ALTER TABLE top_ten_team DROP FOREIGN KEY FK_589AA63E65A720D7');
        $this->addSql('ALTER TABLE top_ten_team DROP FOREIGN KEY FK_589AA63E296CD8AE');
        $this->addSql('DROP TABLE top_ten');
        $this->addSql('DROP TABLE top_ten_team');
    }
}

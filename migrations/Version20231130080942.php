<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130080942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE selection DROP FOREIGN KEY FK_96A50CD78642ED32');
        $this->addSql('ALTER TABLE selection DROP FOREIGN KEY FK_96A50CD7D6365F12');
        $this->addSql('DROP TABLE selection');
        $this->addSql('ALTER TABLE game ADD team_order LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('DROP INDEX UNIQ_3EB4C318B4FCDE07 ON league');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE selection (id INT AUTO_INCREMENT NOT NULL, teams_id INT DEFAULT NULL, leagues_id INT DEFAULT NULL, selected_away INT NOT NULL, selected_home INT NOT NULL, INDEX IDX_96A50CD7D6365F12 (teams_id), INDEX IDX_96A50CD78642ED32 (leagues_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE selection ADD CONSTRAINT FK_96A50CD78642ED32 FOREIGN KEY (leagues_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE selection ADD CONSTRAINT FK_96A50CD7D6365F12 FOREIGN KEY (teams_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE game DROP team_order');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EB4C318B4FCDE07 ON league (league_name)');
    }
}

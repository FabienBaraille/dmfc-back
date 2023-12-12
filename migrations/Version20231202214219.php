<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202214219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bet_top (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, topten_id INT NOT NULL, predicted_ranking LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', validation_status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A7D88378A76ED395 (user_id), INDEX IDX_A7D883782D7DCFB3 (topten_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bet_top ADD CONSTRAINT FK_A7D88378A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE bet_top ADD CONSTRAINT FK_A7D883782D7DCFB3 FOREIGN KEY (topten_id) REFERENCES top_ten (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet_top DROP FOREIGN KEY FK_A7D88378A76ED395');
        $this->addSql('ALTER TABLE bet_top DROP FOREIGN KEY FK_A7D883782D7DCFB3');
        $this->addSql('DROP TABLE bet_top');
    }
}

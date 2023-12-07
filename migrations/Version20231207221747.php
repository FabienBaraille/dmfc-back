<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231207221747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet_top CHANGE validation_status validation_status VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE user ADD score_top SMALLINT DEFAULT NULL, ADD score_po SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet_top CHANGE validation_status validation_status VARCHAR(20) NOT NULL COMMENT \'Saved, Validated, Published\'');
        $this->addSql('ALTER TABLE user DROP score_top, DROP score_po');
    }
}

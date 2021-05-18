<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210518212228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE game DROP INDEX UNIQ_232B318C642B8210, ADD INDEX IDX_232B318C642B8210 (admin_id)');
        $this->addSql('ALTER TABLE game_user CHANGE game_id game_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE round CHANGE game_id game_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_score CHANGE game_id game_id VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE game DROP INDEX IDX_232B318C642B8210, ADD UNIQUE INDEX UNIQ_232B318C642B8210 (admin_id)');
        $this->addSql('ALTER TABLE game_user CHANGE game_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE round CHANGE game_id game_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_score CHANGE game_id game_id INT NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522072555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX game_topic ON round (game_id, topic)');
        $this->addSql('CREATE UNIQUE INDEX round_word ON round_stats (round_id, word)');
        $this->addSql('CREATE UNIQUE INDEX user_game ON user_score (user_id, game_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX game_topic ON round');
        $this->addSql('DROP INDEX round_word ON round_stats');
        $this->addSql('DROP INDEX user_game ON user_score');
    }
}

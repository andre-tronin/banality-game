<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523141929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id VARCHAR(255) NOT NULL, current_round_id INT DEFAULT NULL, admin_id INT NOT NULL, status ENUM(\'start\', \'open\', \'close\', \'end\'), locale ENUM(\'ru\', \'de\', \'en\'), use_dictionary TINYINT(1) DEFAULT \'1\', UNIQUE INDEX UNIQ_232B318C3B78268A (current_round_id), INDEX IDX_232B318C642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id VARCHAR(255) NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, current_word_id INT DEFAULT NULL, game_id VARCHAR(255) NOT NULL, topic VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C5EEEA34495AC608 (current_word_id), INDEX IDX_C5EEEA34E48FD905 (game_id), UNIQUE INDEX game_topic (game_id, topic), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round_stats (id INT AUTO_INCREMENT NOT NULL, round_id INT NOT NULL, word VARCHAR(255) NOT NULL, count SMALLINT NOT NULL, INDEX IDX_81347353A6005CA0 (round_id), UNIQUE INDEX round_word (round_id, word), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', nickname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_score (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id VARCHAR(255) NOT NULL, score SMALLINT NOT NULL, INDEX IDX_D05BCC09A76ED395 (user_id), INDEX IDX_D05BCC09E48FD905 (game_id), UNIQUE INDEX user_game (user_id, game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, round_id INT NOT NULL, word VARCHAR(255) NOT NULL, INDEX IDX_C3F17511A76ED395 (user_id), INDEX IDX_C3F17511A6005CA0 (round_id), UNIQUE INDEX user_round (word, user_id, round_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3B78268A FOREIGN KEY (current_round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34495AC608 FOREIGN KEY (current_word_id) REFERENCES round_stats (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE round_stats ADD CONSTRAINT FK_81347353A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE user_score ADD CONSTRAINT FK_D05BCC09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_score ADD CONSTRAINT FK_D05BCC09E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE word ADD CONSTRAINT FK_C3F17511A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE word ADD CONSTRAINT FK_C3F17511A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34E48FD905');
        $this->addSql('ALTER TABLE user_score DROP FOREIGN KEY FK_D05BCC09E48FD905');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3B78268A');
        $this->addSql('ALTER TABLE round_stats DROP FOREIGN KEY FK_81347353A6005CA0');
        $this->addSql('ALTER TABLE word DROP FOREIGN KEY FK_C3F17511A6005CA0');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34495AC608');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C642B8210');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('ALTER TABLE user_score DROP FOREIGN KEY FK_D05BCC09A76ED395');
        $this->addSql('ALTER TABLE word DROP FOREIGN KEY FK_C3F17511A76ED395');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_user');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE round_stats');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_score');
        $this->addSql('DROP TABLE word');
    }
}

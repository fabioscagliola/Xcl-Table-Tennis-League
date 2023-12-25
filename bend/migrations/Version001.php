<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, league_id INT DEFAULT NULL, p1_id_id INT NOT NULL, p2_id_id INT NOT NULL, winner_id_id INT NOT NULL, date DATETIME NOT NULL, p1_points INT NOT NULL, p2_points INT NOT NULL, INDEX IDX_232B318C58AFC4DE (league_id), INDEX IDX_232B318C83D83377 (p1_id_id), INDEX IDX_232B318CB23029EA (p2_id_id), INDEX IDX_232B318CFC53D4E9 (winner_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE league (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_league (player_id INT NOT NULL, league_id INT NOT NULL, INDEX IDX_A1B455C699E6F5DF (player_id), INDEX IDX_A1B455C658AFC4DE (league_id), PRIMARY KEY(player_id, league_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C58AFC4DE FOREIGN KEY (league_id) REFERENCES league (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C83D83377 FOREIGN KEY (p1_id_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CB23029EA FOREIGN KEY (p2_id_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CFC53D4E9 FOREIGN KEY (winner_id_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE player_league ADD CONSTRAINT FK_A1B455C699E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_league ADD CONSTRAINT FK_A1B455C658AFC4DE FOREIGN KEY (league_id) REFERENCES league (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C58AFC4DE');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C83D83377');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CB23029EA');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CFC53D4E9');
        $this->addSql('ALTER TABLE player_league DROP FOREIGN KEY FK_A1B455C699E6F5DF');
        $this->addSql('ALTER TABLE player_league DROP FOREIGN KEY FK_A1B455C658AFC4DE');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE league');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_league');
    }
}

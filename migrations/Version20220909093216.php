<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220909093216 extends AbstractMigration {
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon_pokemon_types (pokemon_id INT NOT NULL, pokemon_types_id INT NOT NULL, INDEX IDX_4997DD6F2FE71C3E (pokemon_id), INDEX IDX_4997DD6F9D6FAF9 (pokemon_types_id), PRIMARY KEY(pokemon_id, pokemon_types_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon_types (id INT AUTO_INCREMENT NOT NULL, pokemon_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pokemon_pokemon_types ADD CONSTRAINT FK_4997DD6F2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon_pokemon_types ADD CONSTRAINT FK_4997DD6F9D6FAF9 FOREIGN KEY (pokemon_types_id) REFERENCES pokemon_types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon ADD pokedex_id INT NOT NULL');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon_pokemon_types DROP FOREIGN KEY FK_4997DD6F2FE71C3E');
        $this->addSql('ALTER TABLE pokemon_pokemon_types DROP FOREIGN KEY FK_4997DD6F9D6FAF9');
        $this->addSql('DROP TABLE pokemon_pokemon_types');
        $this->addSql('DROP TABLE pokemon_types');
        $this->addSql('ALTER TABLE pokemon DROP pokedex_id');
    }
}

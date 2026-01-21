<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121075102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, synopsis VARCHAR(255) DEFAULT NULL, release_year INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE film_platforme (film_id INT NOT NULL, platforme_id INT NOT NULL, INDEX IDX_D7996D81567F5183 (film_id), INDEX IDX_D7996D814FF12FE6 (platforme_id), PRIMARY KEY (film_id, platforme_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE platforme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E35533B25E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE film_platforme ADD CONSTRAINT FK_D7996D81567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_platforme ADD CONSTRAINT FK_D7996D814FF12FE6 FOREIGN KEY (platforme_id) REFERENCES platforme (id) ON DELETE CASCADE');
        $this->addSql("INSERT INTO utilisateur (email, roles, password) VALUES ('moi@cinescope.com', '[\"ROLE_ADMIN\"]','\$2y\$13\$ElTUj5OqmSnwrviwItmw0O/663Y6bEAyLPhCfZZKmUM1GgD3IFB52')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_platforme DROP FOREIGN KEY FK_D7996D81567F5183');
        $this->addSql('ALTER TABLE film_platforme DROP FOREIGN KEY FK_D7996D814FF12FE6');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE film_platforme');
        $this->addSql('DROP TABLE platforme');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

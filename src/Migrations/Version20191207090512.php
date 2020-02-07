<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\User;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207090512 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN roles CLOB NOT NULL DEFAULT ' . User::ROLE_COMMENTATOR);
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, content, published, author_id, blog_post_id FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL COLLATE BINARY, published DATETIME NOT NULL, author_id INTEGER NOT NULL, blog_post_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO comment (id, content, published, author_id, blog_post_id) SELECT id, content, published, author_id, blog_post_id FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, content, published, author_id, blog_post_id FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, published DATETIME NOT NULL, author_id INTEGER NOT NULL, blog_post_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO comment (id, content, published, author_id, blog_post_id) SELECT id, content, published, author_id, blog_post_id FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, name, email FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, name, email) SELECT id, username, password, name, email FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}

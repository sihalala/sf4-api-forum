<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191208133640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE blog_post_image (blog_post_id INTEGER NOT NULL, image_id INTEGER NOT NULL, PRIMARY KEY(blog_post_id, image_id))');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, name, email, roles, password_change_date, enabled, confirmation_token FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL COLLATE BINARY, password VARCHAR(255) NOT NULL COLLATE BINARY, name VARCHAR(255) NOT NULL COLLATE BINARY, email VARCHAR(255) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:simple_array)
        , password_change_date INTEGER DEFAULT NULL, confirmation_token VARCHAR(40) DEFAULT NULL COLLATE BINARY, enabled BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, name, email, roles, password_change_date, enabled, confirmation_token) SELECT id, username, password, name, email, roles, password_change_date, enabled, confirmation_token FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE blog_post_image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, name, email, roles, password_change_date, enabled, confirmation_token FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:simple_array)
        , password_change_date INTEGER DEFAULT NULL, confirmation_token VARCHAR(40) DEFAULT NULL, enabled BOOLEAN DEFAULT \'TRUE\' NOT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, name, email, roles, password_change_date, enabled, confirmation_token) SELECT id, username, password, name, email, roles, password_change_date, enabled, confirmation_token FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}

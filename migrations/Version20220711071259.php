<?php

declare(strict_types=1);

/*
 * This file is part of mazarini/site.
 *
 * mazarini/site is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * mazarini/site is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with mazarini/site. If not, see <https://www.gnu.org/licenses/>.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220711071259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, slug VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, label VARCHAR(255) NOT NULL, weight INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_7D053A93727ACA70 ON menu (parent_id)');
        $this->addSql('CREATE TABLE page (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, head_title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, content CLOB NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, public_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE user');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201029114253 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE scope (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');


        $this->addSql('INSERT INTO scope (created_at, updated_at, name) VALUES 
        (now(), now(), "@review/backend/urgent"),
        (now(), now(), "@review/backend/small"),
        (now(), now(), "@review/backend")');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE scope');
    }
}

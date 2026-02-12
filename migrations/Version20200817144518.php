<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200817144518 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE review ADD created_at DATETIME NULL, ADD updated_at DATETIME NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE review DROP created_at, DROP updated_at');
    }
}

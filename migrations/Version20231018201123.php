<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231018201123 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE merge_request ADD action VARCHAR(255) NULL, ADD description LONGTEXT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE merge_request DROP action, DROP description');
    }
}

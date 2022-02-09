<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220208172125 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            UPDATE activity 
            SET changes = JSON_REMOVE(changes, \'$.category\')
            WHERE `type` = \'edit_notelist\'
        ');
    }

    public function down(Schema $schema): void
    {
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211026164203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Note entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE note (
                id INT AUTO_INCREMENT NOT NULL, 
                title VARCHAR(100) NOT NULL, 
                text LONGTEXT NOT NULL, 
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE note');
    }
}

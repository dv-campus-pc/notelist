<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211214184201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added entity activity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, method VARCHAR(10) NOT NULL, url LONGTEXT NOT NULL, created_at DATETIME NOT NULL, ip VARCHAR(45) DEFAULT NULL, status_code INT NOT NULL, INDEX IDX_AC74095AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE activity');
    }
}

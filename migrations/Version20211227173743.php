<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211227173743 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE note_user (note_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2DE9C71126ED0855 (note_id), INDEX IDX_2DE9C711A76ED395 (user_id), PRIMARY KEY(note_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note_user ADD CONSTRAINT FK_2DE9C71126ED0855 FOREIGN KEY (note_id) REFERENCES note (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note_user ADD CONSTRAINT FK_2DE9C711A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note CHANGE user_id owner_id INT NOT NULL');
        $this->addSql('
            INSERT INTO note_user (note_id, user_id)
            SELECT note.id AS note_id, note.owner_id AS user_id
            FROM note
        ');
        $this->addSql('ALTER TABLE note RENAME INDEX idx_cfbdfa14a76ed395 TO IDX_CFBDFA147E3C61F9');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note CHANGE owner_id user_id INT NOT NULL');
        $this->addSql('DROP TABLE note_user');
        $this->addSql('ALTER TABLE note RENAME INDEX idx_cfbdfa147e3c61f9 TO IDX_CFBDFA14A76ED395');
    }
}

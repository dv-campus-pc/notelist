<?php

declare(strict_types=1);

namespace App\Entity\Activity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Note;
use App\Entity\User;

/**
 * @ORM\Entity()
 */
class EditNoteActivity extends Activity
{
    /**
     * @ORM\ManyToOne(targetEntity=Note::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Note $note;

    public function __construct(User $user, Note $note) {
        parent::__construct($user);
        $this->note = $note;
    }

    public function getNote(): Note
    {
        return $this->note;
    }

    public function setNote(Note $note): self
    {
        $this->note = $note;

        return $this;
    }
}

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

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $changes = [];

    public function __construct(User $user, Note $note, array $changes) {
        parent::__construct($user);
        $this->note = $note;
        $this->changes = $changes;
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

    public function getChanges(): ?array
    {
        return $this->changes;
    }

    public function setChanges(?array $changes): self
    {
        $this->changes = $changes;

        return $this;
    }
}

<?php

namespace App\EventListener;

use App\Entity\Note;
use App\Service\NoteActivityService;

class EditNoteActivityListener
{
    private NoteActivityService $noteActivityService;

    public function __construct(NoteActivityService $noteActivityService)
    {
        $this->noteActivityService = $noteActivityService;
    }

    public function postUpdate(Note $note): void
    {
        $this->noteActivityService->createNoteEditActivity($note);
    }
}

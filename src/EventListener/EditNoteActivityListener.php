<?php

namespace App\EventListener;

use App\Entity\Note;
use App\Service\NoteActivityService;
use Doctrine\ORM\EntityManagerInterface;

class EditNoteActivityListener
{
    private NoteActivityService $noteActivityService;
    private EntityManagerInterface $em;

    public function __construct(NoteActivityService $noteActivityService, EntityManagerInterface $em)
    {
        $this->noteActivityService = $noteActivityService;
        $this->em = $em;
    }

    public function postUpdate(Note $note): void
    {
        $uow =  $this->em->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($note);
        $this->noteActivityService->createNoteEditActivity($note, $changes);
    }
}

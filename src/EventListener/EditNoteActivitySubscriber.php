<?php

namespace App\EventListener;

use App\Entity\Note;
use App\Service\NoteActivityService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EditNoteActivitySubscriber implements EventSubscriberInterface
{
    private NoteActivityService $noteActivityService;

    public function __construct(NoteActivityService $noteActivityService)
    {
        $this->noteActivityService = $noteActivityService;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Note) {
            return;
        }

        $this->noteActivityService->createNoteEditActivity($entity);
    }
}

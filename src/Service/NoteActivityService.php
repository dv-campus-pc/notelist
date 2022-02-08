<?php

namespace App\Service;

use App\Entity\Activity\EditNoteActivity;
use App\Entity\Category;
use App\Entity\Note;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NoteActivityService
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function createEditNoteActivity(Note $note, array $changes)
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        if (!$user instanceof User) {
            throw new HttpException(400, 'User not exists in request');
        }

        $activity = new EditNoteActivity($user, $note, $this->prepareChanges($changes));

        $this->em->persist($activity);
        $this->em->flush();
    }

    private function prepareChanges(array $changes): array
    {
        $result = [];
        foreach ($changes as $key => $itemChanges) {
            if ($key === 'category') {
                $result[$key] = $this->prepareCategory($itemChanges);
                continue;
            }

            $result[$key] = $itemChanges;
        }

        return $result;
    }

    /**
     * @param Category[] $categories
     * @return array
     */
    private function prepareCategory(array $categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $result[] = $category->getId();
        }

        return $result;
    }

}
<?php

namespace App\Service;

use App\Entity\Activity\VisitActivity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivityService
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function createFromRequestResponse(Request $request, Response $response)
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        $activity = new VisitActivity(
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $request->getClientIp(),
            $user instanceof User ? $user : null
        );

        $this->em->persist($activity);
        $this->em->flush();
    }
}
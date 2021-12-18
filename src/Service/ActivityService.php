<?php

namespace App\Service;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class ActivityService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createFromRequestResponse(Request $request, Response $response)
    {
        $activity = new Activity(
            $request->getMethod(),
            $request->getUri(),
            new DateTime,
            $response->getStatusCode(),
            $request->getClientIp()
            //TODO add user here;
        );

        $this->em->persist($activity);
        $this->em->flush();
    }
}
<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/activity", name="activity_")
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/visit", name="visit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function visit(EntityManagerInterface $em): Response
    {
        return $this->render('activity/visit.html.twig', [
            'data' => $em->getRepository(Activity::class)->getVisitActivityData()
        ]);
    }

    /**
     * @Route("/note", name="note")
     * @IsGranted("ROLE_USER")
     */
    public function note(EntityManagerInterface $em): Response
    {
        return $this->render('activity/note.html.twig', [
            'data' => $em->getRepository(Activity::class)->getNoteActivityData($this->getUser())
        ]);
    }
}

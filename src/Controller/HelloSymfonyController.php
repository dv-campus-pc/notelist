<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloSymfonyController extends AbstractController
{
    /**
     * @Route("/hello/symfony2", name="hello_symfony")
     */
    public function index(): Response
    {
        return $this->render('hello_symfony/index.html.twig', [
            'controller_name' => 'HelloSymfonyController',
        ]);
    }
}

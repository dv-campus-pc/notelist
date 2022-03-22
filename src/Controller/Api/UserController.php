<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/registration", name="registration", methods={"POST"})
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function registration(Request $request, UserService $userService, SerializerInterface $serializer): Response {
        $requestContent = $serializer->decode($request->getContent(), 'json');
        $user = $userService->createAndFlush(
            (string) $requestContent['password'] ?? '',
            (string) $requestContent['username'] ?? ''
        );

        return new Response($serializer->serialize($user, 'json', [
            'groups' => ['API']
        ]));
    }
}

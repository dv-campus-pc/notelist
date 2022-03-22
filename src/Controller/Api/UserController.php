<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\API\ApiResponse;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractApiController
{
    /**
     * @Route("/registration", name="registration", methods={"POST"})
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function registration(Request $request, UserService $userService): Response {
        $requestContent = $this->serializer->decode($request->getContent(), 'json');
        $user = $userService->createAndFlush(
            (string) $requestContent['password'] ?? '',
            (string) $requestContent['username'] ?? ''
        );

        return new Response($this->serializer->serialize($user, 'json', [
            'groups' => ['API']
        ]));
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(UserService $userService): Response {
        $users = $userService->getUserList();

        return new ApiResponse($this->serializer->serialize($users, 'json', [
            'groups' => ['API'],
        ]));
    }
}

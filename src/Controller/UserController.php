<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\FlashMessagesEnum;
use App\Service\UserService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/registration", name="registration", methods={"POST", "GET"})
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function registration(Request $request, UserService $userService): Response {
        $userService->createAndFlush(
            (string) $request->request->get('password'),
            (string) $request->request->get('username')
        );

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/login", name="login")
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $this->addFlash(FlashMessagesEnum::FAIL, $error
            ? $error->getMessage()
            : 'You should be authenticated'
        );

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function logout(): void
    {
        throw new Exception('Unreachable statement');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/registration", name="registration", methods={"POST"})
     */
    public function registration(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): Response {
        $plainPassword = $request->request->get('password');
        /** @var ConstraintViolationList $passwordErrors */
        $passwordErrors = $validator->validate($plainPassword, [
            new Assert\NotBlank(['message' => "Password should not be blank"]),
            new Assert\Length([
                'min' => 3,
                'max' => 20,
                'minMessage' => "Password should be at least {{ limit }} characters long",
                'maxMessage' => "Password cannot be longer than {{ limit }} characters"
            ])
        ]);
        if ($passwordErrors->count()) {
            foreach ($passwordErrors as $error) {
                $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
            }

            return $this->redirectToRoute('page_home');
        }

        $username = $request->request->get('username');
        /** @var ConstraintViolationList $passwordErrors */
        $usernameErrors = $validator->validate($username, [
            new Assert\NotBlank(['message' => "Username should not be blank"]),
            new Assert\Length([
                'min' => 3,
                'max' => 20,
                'minMessage' => "Username should be at least {{ limit }} characters long",
                'maxMessage' => "Username cannot be longer than {{ limit }} characters"
            ])
        ]);
        if ($usernameErrors->count()) {
            foreach ($usernameErrors as $error) {
                $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
            }

            return $this->redirectToRoute('page_home');
        }

        $user = new User($username);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        // TODO: investigate why it doesn't work properly
        $userErrors = $validator->validate($user);
        foreach ($userErrors as $error) {
            $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$userErrors->count()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash(FlashMessagesEnum::SUCCESS, "You have been registered!");
        }

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/login", name="login")
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
     */
    public function logout(): void
    {
        throw new Exception('Unreachable statement');
    }
}

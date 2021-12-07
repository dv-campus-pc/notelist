<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Enum\FlashMessagesEnum;
use App\Exception\UserValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserService
{
    private ValidatorInterface $validator;
    private Session $session;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        SessionInterface $session,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->session = $session;
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
    }

    public function createAndFlushOnHttpRequest(string $plainPassword, string $username): void
    {
        try {
            $this->createAndFlush($plainPassword, $username);
            $this->session->getFlashBag()->add(FlashMessagesEnum::SUCCESS, "You have been registered!");
        } catch (UserValidationException $exception) {
            $this->session->getFlashBag()->add(FlashMessagesEnum::FAIL, $exception->getMessage());
        }
    }

    public function createAndFlush(string $plainPassword, string $username): void
    {
        $this->validateUserPassword($plainPassword);
        $user = $this->create($plainPassword, $username);
        $this->validateUser($user);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function create(string $plainPassword, string $username): User
    {
        $user = new User($username);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    private function validateUserPassword(string $plainPassword)
    {
        /** @var ConstraintViolationList $passwordErrors */
        $passwordErrors = $this->validator->validate($plainPassword, [
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
                throw new UserValidationException($error->getMessage());
            }
        }
    }

    private function validateUser(User $user)
    {
        $userErrors = $this->validator->validate($user);
        foreach ($userErrors as $error) {
            throw new UserValidationException($error->getMessage());
        }
    }
}

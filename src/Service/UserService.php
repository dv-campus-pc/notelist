<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserService
{
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
    }

    public function createAndFlush(string $plainPassword, string $username): User
    {
        $user = $this->create($plainPassword, $username);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function create(string $plainPassword, string $username): User
    {
        $user = new User($username);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);
        $this->validateUser($user, $plainPassword);

        return $user;
    }

    private function validateUserPassword(string $plainPassword): ConstraintViolationListInterface
    {
        return $this->validator->validate($plainPassword, [
            new Assert\NotBlank(['message' => "Password should not be blank"]),
            new Assert\Length([
                'min' => 3,
                'max' => 20,
                'minMessage' => "Password should be at least {{ limit }} characters long",
                'maxMessage' => "Password cannot be longer than {{ limit }} characters"
            ])
        ]);
    }

    private function validateUser(User $user, string $plainPassword)
    {
        $userErrors = $this->validator->validate($user);
        $userErrors->addAll(
            $this->validateUserPassword($plainPassword)
        );

        if ($userErrors->count()) {
            throw new ValidationException('', $userErrors);
        }
    }

    /**
     * @return User[]
     */
    public function getUserList(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }
}

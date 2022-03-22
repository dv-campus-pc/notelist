<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationList;
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
        $this->validateUserPassword($plainPassword);
        $user = new User($username);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );
        $user->setPassword($hashedPassword);
        $this->validateUser($user);

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
                throw new HttpException(400, $error->getMessage());
            }
        }
    }

    private function validateUser(User $user)
    {
        $userErrors = $this->validator->validate($user);
        foreach ($userErrors as $error) {
            throw new HttpException(400, $error->getMessage());
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

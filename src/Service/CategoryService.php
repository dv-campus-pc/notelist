<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private ValidatorInterface $validator;
    private Session $session;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        SessionInterface $session,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->session = $session;
        $this->em = $em;
    }

    public function createAndFlush(string $name, UserInterface $user): void
    {
        $category = new Category($name, $user);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($category);
        foreach ($errors as $error) {
            $this->session->getFlashBag()->add(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$errors->count()) {
            $this->em->persist($category);
            $this->em->flush();

            $this->session->getFlashBag()->add(FlashMessagesEnum::SUCCESS, sprintf('Category %s was created', $name));
        }
    }
}

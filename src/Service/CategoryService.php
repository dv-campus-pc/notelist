<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->em = $em;
    }

    public function createAndFlush(string $name): void
    {
        $category = new Category($name);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($category);
        foreach ($errors as $error) {
            throw new HttpException(400, $error->getMessage());
        }

        $this->em->persist($category);
        $this->em->flush();
    }
}

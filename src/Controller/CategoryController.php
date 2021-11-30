<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/category", name="category_")
 *
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route(name="add", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $name = $request->request->get('name');

        $category = new Category($name, $this->getUser());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($category);
        foreach ($errors as $error) {
            $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$errors->count()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was created', $name));
        }

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/{id}", name="delete", requirements={"categoryId"="\d+"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was removed', $category->getTitle()));

        return $this->redirectToRoute('page_home');
    }
}

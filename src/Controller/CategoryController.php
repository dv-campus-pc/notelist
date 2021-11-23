<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route(name="add", methods={"POST"})
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('name');

        $em->persist(new Category($name));
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was created', $name));

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/{categoryId}", name="delete", requirements={"categoryId"="\d+"})
     */
    public function delete(string $categoryId, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was removed', $category->getTitle()));

        return $this->redirectToRoute('page_home');
    }
}

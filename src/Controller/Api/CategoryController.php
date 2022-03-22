<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Category;
use App\Model\API\ApiResponse;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 *
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractApiController
{
    /**
     * @Route(name="add", methods={"POST"})
     */
    public function create(Request $request, CategoryService $categoryService): Response
    {
        $requestContent = $this->serializer->decode($request->getContent(), 'json');
        $categoryName = $requestContent['name'] ?? null;
        $category = $categoryService->createAndFlush($categoryName);

        return new ApiResponse($this->serializer->serialize($category, 'json', [
            'groups' => ['API']
        ]));
    }

    /**
     * @Route(name="get", methods={"GET"})
     */
    public function getAction(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findBy([
            'user' => $this->getUser()
        ]);

        return new ApiResponse($this->serializer->serialize($categories, 'json', [
            'groups' => ['API']
        ]));
    }

    /**
     * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        return new ApiResponse();
    }
}

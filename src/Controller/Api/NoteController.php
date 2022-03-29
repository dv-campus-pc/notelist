<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Note;
use App\Exception\ValidationException;
use App\Model\API\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/note", name="note_")
 */
class NoteController extends AbstractApiController
{
    /**
     * @Route(name="create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {
        /** @var Note $note */
        $note = $this->serializer->deserialize($request->getContent(), Note::class, 'json');

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($note);
        if ($errors->count()) {
            throw new ValidationException('', $errors);
        }

        $note->setOwner($this->getUser());
        $em->persist($note);
        $em->flush();

        return new ApiResponse($this->serializer->serialize($note, 'json', [
            'groups' => ['API'],
        ]));
    }

    /**
     * @Route(name="list", methods={"GET"})
     */
    public function list(EntityManagerInterface $em): Response
    {
        return new ApiResponse($this->serializer->serialize(
            $em->getRepository(Note::class)->selectByUser($this->getUser())->getQuery()->getResult(),
            'json',
            ['groups' => 'API']
        ));
    }
}

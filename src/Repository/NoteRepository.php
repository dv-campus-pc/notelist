<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function selectByUser(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('note')
            ->select('note')
            ->join('note.users', 'user')
            ->where('user = :user')
            ->orderBy('note.id', 'DESC')
            ->setParameter(':user', $user);
    }

    public function findByUser(UserInterface $user): array
    {
        return $this->selectByUser($user)
            ->getQuery()
            ->getResult();
    }

    public function selectByCategoryAndUser(Category $category, UserInterface $user): QueryBuilder
    {
        return $this->selectByUser($user)
            ->andWhere('note.category = :category')
            ->setParameter(':category', $category);
    }
}

<?php

namespace App\Repository;

use App\Entity\Activity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @return QueryBuilder
     */
    public function selectVisitActivityData(): QueryBuilder
    {
        return $this->selectActivityData()
            ->where('activity INSTANCE OF App\Entity\Activity\VisitActivity');
    }

    /**
     * @return QueryBuilder
     */
    public function selectNoteActivityData(): QueryBuilder
    {
        return $this->selectActivityData()
            ->andWhere('activity INSTANCE OF App\Entity\Activity\EditNoteActivity');
    }

    /**
     * @return QueryBuilder
     */
    private function selectActivityData(): QueryBuilder
    {
        return $this->createQueryBuilder('activity')
            ->orderBy('activity.createdAt', 'DESC');
    }

}

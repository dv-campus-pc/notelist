<?php

namespace App\Repository;

use App\Entity\Activity\Activity;
use App\Entity\Activity\VisitActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getVisitActivityData(): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare('
            SELECT * FROM `activity`
            WHERE type = :type
            ORDER BY created_at DESC
        ');
        $result = $stmt->executeQuery([
            'type' => 'visit'
        ]);

        return $result->fetchAllAssociative();
    }

    /**
     * @param int $itemsPerPage
     * @param int $offset
     *
     * @return VisitActivity[]
     */
    public function getVisitActivityDataQB(int $itemsPerPage = 10, int $offset = 0): array
    {
        return $this->createQueryBuilder('activity')
            ->orderBy('activity.createdAt', 'DESC')
            ->setMaxResults($itemsPerPage)
            ->setFirstResult($offset)
            ->where('activity INSTANCE OF App\Entity\Activity\VisitActivity')
            ->getQuery()
            ->getResult();
    }

    public function getNoteActivityData(UserInterface $user): array
    {
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare('
            SELECT 
            activity.created_at AS created_at,
            note.title AS note_name
                   
            FROM activity
            
            JOIN note ON note.id = activity.note_id
            
            WHERE type = :type
            AND activity.user_id = :user
            ORDER BY created_at DESC
        ');
        $result = $stmt->executeQuery([
            'type' => 'edit_notelist',
            'user' => $user->getId()
        ]);

        return $result->fetchAllAssociative();
    }
}

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
        return $this->createQueryBuilder('activity')
            ->orderBy('activity.createdAt', 'DESC')
            ->where('activity INSTANCE OF App\Entity\Activity\VisitActivity');
    }

    /**
     * @return QueryBuilder
     */
    public function selectNoteActivityData(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('activity')
            ->orderBy('activity.createdAt', 'DESC')
            ->where('activity.user = :user')
            ->andWhere('activity INSTANCE OF App\Entity\Activity\EditNoteActivity')
            ->setParameter('user', $user);
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

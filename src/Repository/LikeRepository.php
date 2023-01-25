<?php

namespace App\Repository;

use App\Entity\Like;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 *
 * @method Like|null find($id, $lockMode = null, $lockVersion = null)
 * @method Like|null findOneBy(array $criteria, array $orderBy = null)
 * @method Like[]    findAll()
 * @method Like[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function save(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Like $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Like[] Returns an array of Like objects
     */
    public function findVideosLikedByCurrentUserOrderByDate(int $currentUserId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $currentUserId)
            ->join('l.video', 'v')
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Like[] Returns an array of Like objects
     */
    public function findVideosLikedByCurrentUserOrderByViews(int $currentUserId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $currentUserId)
            ->join('l.video', 'v')
            ->orderBy('v.numberOfView', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Like[] Returns an array of Like objects
     */
    public function findVideosLikedByCurrentUserOrderByLikes(int $currentUserId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->setParameter('user', $currentUserId)
            ->join('l.video', 'v')
            ->orderBy('v.numberOfLike', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     *  Get all videos added less than 7 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getLikesAddedInPast7Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(l.id)
            FROM App\Entity\Like l
            WHERE l.createdAt > :date'
        )->setParameter('date', new DateTime('-7 days'));

        return $query->getScalarResult();
    }

    /**
     * Get all videos added less than 30 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getLikesAddedInPast30Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(l.id)
            FROM App\Entity\Like l
            WHERE l.createdAt > :date'
        )->setParameter('date', new DateTime('-30 days'));

        return $query->getScalarResult();
    }
}

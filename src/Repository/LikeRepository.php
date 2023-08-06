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

    public function getNumberOfLikeFromDate(DateTime $date = null): int
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('count(l)');
        if ($date) {
            $qb->where('l.createdAt > :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countLikeByMonth(): array
    {
        $lastYear = new DateTime('first day of next month');
        $lastYear->modify('-12 months');

        $qb = $this->createQueryBuilder('l');
        $qb->select('MONTH(l.createdAt) AS month')
            ->addSelect('COUNT(l) AS numberOfLikes')
            ->where('l.createdAt >= :startDate')
            ->setParameter('startDate', $lastYear)
            ->groupBy('month');

        return $qb->getQuery()->getResult();
    }
}

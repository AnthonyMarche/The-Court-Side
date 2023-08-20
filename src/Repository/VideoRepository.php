<?php

namespace App\Repository;

use App\Entity\Video;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 *
 * @method Video|null find($id, $lockMode = null, $lockVersion = null)
 * @method Video|null findOneBy(array $criteria, array $orderBy = null)
 * @method Video[]    findAll()
 * @method Video[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function getVideosAddedFromDate(DateTime $date = null): int
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('count(v)');
        if ($date) {
            $qb->where('v.createdAt > :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function save(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Video $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getVideoByCategory(string $slug, string $orderBy): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.category', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('v.' . $orderBy, 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getVideoBySort(string $orderBy): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.' . $orderBy, 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getPrivateVideo(string $orderBy): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.isPrivate = true')
            ->orderBy('v.' . $orderBy, 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getVideoByTag(string $slug, string $orderBy): array
    {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->where('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->orderBy('v.' . $orderBy, 'DESC')
                ->getQuery()
                ->getResult();
    }

    public function getVideoBySearch(string $search, string $orderBy): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.tag', 't')
            ->join('v.category', 'c')
            ->where('v.title LIKE :search')
            ->orWhere('t.name LIKE :search')
            ->orWhere('c.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('v.' . $orderBy, 'DESC')
            ->groupBy('v.' . $orderBy)
            ->getQuery()
            ->getResult();
    }

    public function getLikedVideoByUser(int $userId, string $orderBy): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.likes', 'l')
            ->where('l.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('v.' . $orderBy, 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getSimilarVideosByCategory(int $categoryId, int $videoId): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.category = :categoryId')
            ->andWhere('v.id != :id')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('id', $videoId)
            ->orderBy('v.createdAt', 'DESC')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }
}

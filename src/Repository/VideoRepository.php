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

    public function findCategoryVideosOrderByViews(string $slug): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.category', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('v.numberOfView', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCategoryVideosOrderByLikes(string $slug): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.category', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('v.numberOfLike', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCategoryVideosOrderByDate(string $slug): array
    {
        return $this->createQueryBuilder('v')
            ->join('v.category', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOrderedTagVideos(string $sort, string $slug): array
    {
        if ($sort === 'recent') {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->andWhere('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->orderBy('v.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } elseif ($sort === 'views') {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->andWhere('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->orderBy('v.numberOfView', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->andWhere('t.slug = :slug')
                ->setParameter('slug', $slug)
                ->orderBy('v.numberOfLike', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

    public function findOrderedVideosBySearch(string $sort, string $search): array
    {
        if ($sort === 'recent') {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->join('v.category', 'c')
                ->where('v.title LIKE :search')
                ->orWhere('t.name LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('v.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } elseif ($sort === 'likes') {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->join('v.category', 'c')
                ->where('v.title LIKE :search')
                ->orWhere('t.name LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('v.numberOfLike', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('v')
                ->join('v.tag', 't')
                ->join('v.category', 'c')
                ->where('v.title LIKE :search')
                ->orWhere('t.name LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->orderBy('v.numberOfView', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * @return Video[] Returns an array of Like objects
     */
    public function findOrderedVideosLikedByCurrentUser(string $sort, int $currentUserId): array
    {
        if ($sort === 'recent') {
            return $this->createQueryBuilder('v')
                ->join('v.likes', 'l')
                ->andWhere('l.user = :user')
                ->setParameter('user', $currentUserId)
                ->orderBy('v.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        } elseif ($sort === 'views') {
            return $this->createQueryBuilder('v')
                ->join('v.likes', 'l')
                ->andWhere('l.user = :user')
                ->setParameter('user', $currentUserId)
                ->orderBy('v.numberOfView', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('v')
                ->join('v.likes', 'l')
                ->andWhere('l.user = :user')
                ->setParameter('user', $currentUserId)
                ->orderBy('v.numberOfLike', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

    public function findSimilarVideosByCategory(int $videoCategoryId, int $videoId): array
    {
        return $this->createQueryBuilder('v')
            ->setMaxResults(4)
            ->where('v.category = :categoryId')
            ->andWhere('v.id != :id')
            ->setParameter('categoryId', $videoCategoryId)
            ->setParameter('id', $videoId)
            ->orderBy('v.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

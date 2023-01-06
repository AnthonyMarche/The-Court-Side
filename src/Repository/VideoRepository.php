<?php

namespace App\Repository;

use App\Entity\Video;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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

    /**
     *  Get all videos added less than 7 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getVideosAddedInPast7Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(v.id)
            FROM App\Entity\Video v
            WHERE v.createdAt > :date'
        )->setParameter('date', new DateTime('-7 days'));

        return $query->getScalarResult();
    }

    /**
     * Get all videos added less than 30 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getVideosAddedInPast30Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(v.id)
            FROM App\Entity\Video v
            WHERE v.createdAt > :date'
        )->setParameter('date', new DateTime('-30 days'));

        return $query->getScalarResult();
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

    /**
     * @throws Exception
     */
    public function getVideosOrderByViews(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
        FROM video_user vu
        JOIN video v on v.id = vu.video_id
        JOIN user u on u.id = vu.user_id
        ORDER BY v.number_of_view DESC';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getVideosOrderByLikes(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
        FROM video_user vu
        JOIN video v on v.id = vu.video_id
        JOIN user u on u.id = vu.user_id
        GROUP BY vu.video_id
        ORDER BY COUNT(vu.video_id) DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getVideosOrderByDate(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
        FROM video_user vu
        JOIN video v on v.id = vu.video_id
        JOIN user u on u.id = vu.user_id
        ORDER BY v.created_at DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }
}

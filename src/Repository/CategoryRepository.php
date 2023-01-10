<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function getCategoryVideosOrderByViews(string $slug): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
                FROM category c
                JOIN video v on c.id = v.category_id
                WHERE c.slug = :slug
                ORDER BY v.number_of_view DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['slug' => $slug]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getCategoryVideosOrderByLikes(string $slug): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
                FROM category c
                JOIN video v on c.id = v.category_id
                JOIN video_user vu on v.id = vu.video_id
                WHERE c.slug = :slug
                GROUP BY vu.video_id
                ORDER BY COUNT(vu.video_id) DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['slug' => $slug]);
        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getCategoryVideosOrderByDate(string $slug): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT *
                FROM category c
                JOIN video v on c.id = v.category_id
                WHERE c.slug = :slug
                ORDER BY v.created_at DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['slug' => $slug]);
        return $resultSet->fetchAllAssociative();
    }
}

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

    /**
     * Récupère tous les utilisateurs inscrits (createAt) mois par mois, depuis douze mois
     * /!\ Le mois courant n'est pas pris en compte
     * @return array
     * @throws \Exception
     */
    public function getLikesMonthByMonth()
    {
        $likes = [];
        $likesCount = [];

        for ($i = 1; $i <= 12; $i++) {
            // Premier jour d'un mois
            $firstDayOfMonth = new DateTime("first day of " . $i . " month ago");
            // Dernier jour d'un mois
            $lastDayOfMonth = new DateTime("last day of " . $i . " month ago");
            // Initialise le querybuilder avec la table Like
            $qb = $this->createQueryBuilder('l');
            // Ajoute une condition pour sélectionner les likes entre deux dates
            $qb->select('count(l.id)');
            $qb->andWhere('l.createdAt >= :firstDayOfMonth')
                ->andWhere('l.createdAt <= :lastDayOfMonth')
                ->setParameter('firstDayOfMonth', $firstDayOfMonth)
                ->setParameter('lastDayOfMonth', $lastDayOfMonth);
            $likes[] = $qb->getQuery()->getResult();
        }
        // Met les résultats dans un tableau simplifié
        // ($likes a trois niveaux de profondeur, $likesCount un seul niveau)
        for ($j = 0; $j < count($likes); $j++) {
            $likesCount[] = $likes[$j][0][1];
        }
        // on permute les valeurs pour les mettre dans l'ordre chronologique
        return array_reverse($likesCount);
    }
}

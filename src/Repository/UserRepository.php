<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     *  Get all users who registered less than 7 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getUsersRegisteredInPast7Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(u.id)
            FROM App\Entity\User u
            WHERE u.createdAt > :date'
        )->setParameter('date', new DateTime('-7 days'));

        return $query->getScalarResult();
    }

    /**
     * Get all users who registered less than 30 days ago ('created_at')
     * @return float|int|mixed[]|string
     */
    public function getUsersRegisteredInPast30Days()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(u.id)
            FROM App\Entity\User u
            WHERE u.createdAt > :date'
        )->setParameter('date', new DateTime('-30 days'));

        return $query->getScalarResult();
    }

    /**
     * Récupère tous les utilisateurs inscrits (createAt) mois par mois, depuis douze mois
     * /!\ Le mois courant n'est pas pris en compte
     * @return array
     * @throws \Exception
     */
    public function getUsersSubscriptionsMonthByMonth()
    {
        $users = [];
        $userCount = [];

        for ($i = 1; $i <= 12; $i++) {
            // Premier jour d'un mois
            $firstDayOfMonth = new DateTime("first day of " . $i . " month ago");
            // Dernier jour d'un mois
            $lastDayOfMonth = new DateTime("last day of " . $i . " month ago");
            // Initialise le querybuilder avec la table User
            $qb = $this->createQueryBuilder('u');
            // Ajoute une condition pour sélectionner les utilisateurs entre deux dates
            $qb->select('count(u.id)');
            $qb->andWhere('u.createdAt >= :firstDayOfMonth')
                ->andWhere('u.createdAt <= :lastDayOfMonth')
                ->setParameter('firstDayOfMonth', $firstDayOfMonth)
                ->setParameter('lastDayOfMonth', $lastDayOfMonth);
            $users[] = $qb->getQuery()->getResult();
        }
        // Met les résultats dans un tableau simplifié
        // ($users a trois niveaux de profondeur, $userCount un seul niveau)
        for ($j = 0; $j < count($users); $j++) {
            $userCount[] = $users[$j][0][1];
        }
        // on permute les valeurs pour les mettre dans l'ordre chronologique
        return array_reverse($userCount);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $userVideos = $entity->getVideos();
        foreach ($userVideos as $video) {
            $video->setUser(null);
        }


        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    // Get email and registered date for all users with role 'user'
    public function findUsersToExport(): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.email', 'u.createdAt')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%[]%')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

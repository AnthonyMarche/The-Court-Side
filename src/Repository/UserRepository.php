<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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

    public function countRegisteredUserFromDate(DateTime $date = null): int
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('count(u)');
        if ($date) {
            $qb->where('u.createdAt > :date')
                ->setParameter('date', $date);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countUsersSubscriptionsByMonth(): array
    {
        $lastYear = new DateTime('first day of next month');
        $lastYear->modify('-12 months');

        $qb = $this->createQueryBuilder('u');
        $qb->select('MONTH(u.createdAt) AS month')
            ->addSelect('COUNT(u) AS numberOfUsers')
            ->where('u.createdAt >= :startDate')
            ->setParameter('startDate', $lastYear)
            ->groupBy('month');

        return $qb->getQuery()->getResult();
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
}

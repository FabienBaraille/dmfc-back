<?php

namespace App\Repository;

use App\Entity\BetTop;
use App\Entity\User;
use App\Entity\TopTen;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetTop>
 *
 * @method BetTop|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetTop|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetTop[]    findAll()
 * @method BetTop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetTopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetTop::class);
    }

    public function add(BetTop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BetTop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find all top10 bet made by a player
     *
     * @param User $user user entity
     * @return array of top 10 bet
     */
    public function findByPlayerId(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.User = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all bets made for a Top 10
     *
     * @param TopTen $topten top ten entity
     * @return array of top 10 bet
     */
    public function findByTopten(TopTen $topten): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.topten = :topten')
            ->setParameter('topten', $topten)
            ->getQuery()
            ->getResult();
    }

    public function findByConference($conf): array
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select('b')
            // Equal to INNER JOIN `top_ten` as t ON b.topten_id = t.id
            ->join('b.topten', 't')
            ->where('t.conference = :conference')
            ->setParameter('conference', $conf);

        $query = $qb->getQuery();

        return $query->getResult();
    }

//    /**
//     * @return BetTop[] Returns an array of BetTop objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BetTop
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\TopTen;
use App\Entity\Round;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TopTen>
 *
 * @method TopTen|null find($id, $lockMode = null, $lockVersion = null)
 * @method TopTen|null findOneBy(array $criteria, array $orderBy = null)
 * @method TopTen[]    findAll()
 * @method TopTen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopTenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TopTen::class);
    }

    public function add(TopTen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TopTen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère toutes les topten associés à un round donné.
     *
     * @param Round $round Le round pour lequel vous souhaitez récupérer les parties.
     *
     * @return TopTen[] Un tableau de topten associés au round.
     */
    public function findByRound(Round $round): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.round = :round')
            ->setParameter('round', $round)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return TopTen[] Returns an array of TopTen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TopTen
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Ean;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ean|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ean|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ean[]    findAll()
 * @method Ean[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ean::class);
    }

    // /**
    //  * @return Ean[] Returns an array of Ean objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ean
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

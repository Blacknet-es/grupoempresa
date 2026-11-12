<?php

namespace App\Repository;

use App\Entity\CuentaBancaria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CuentaBancaria|null find($id, $lockMode = null, $lockVersion = null)
 * @method CuentaBancaria|null findOneBy(array $criteria, array $orderBy = null)
 * @method CuentaBancaria[]    findAll()
 * @method CuentaBancaria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CuentaBancariaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CuentaBancaria::class);
    }

    // /**
    //  * @return CuentaBancaria[] Returns an array of CuentaBancaria objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CuentaBancaria
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

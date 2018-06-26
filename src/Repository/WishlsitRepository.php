<?php

namespace App\Repository;

use App\Entity\Wishlsit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Wishlsit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Wishlsit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Wishlsit[]    findAll()
 * @method Wishlsit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlsitRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Wishlsit::class);
    }

//    /**
//     * @return Wishlsit[] Returns an array of Wishlsit objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Wishlsit
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findPaginatedProduits(int $page, int $limit): array
{
    $query = $this->createQueryBuilder('p')
        ->where('p.name IS NOT NULL') 
        ->orderBy('p.created_at', 'DESC') 
        ->getQuery()
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

    $paginator = new Paginator($query, true);

    return [
        'data' => iterator_to_array($paginator),
        'total' => count($paginator),
    ];
}
public function findLastThreeProducts(): array
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.image', 'i') // jointure avec l'entité Image
        ->addSelect('i') // sélectionner les images en plus des produits
        ->where('p.name IS NOT NULL')
        ->orderBy('p.created_at', 'DESC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
}


}

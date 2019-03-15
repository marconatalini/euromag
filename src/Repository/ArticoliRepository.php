<?php

namespace App\Repository;

use App\Entity\Articoli;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Articoli|null find($id, $lockMode = null, $lockVersion = null)
 * @method Articoli|null findOneBy(array $criteria, array $orderBy = null)
 * @method Articoli[]    findAll()
 * @method Articoli[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticoliRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Articoli::class);
    }

    /**
     * @return Pagerfanta
     */
    public function listAll(int $page=1) :Pagerfanta
    {
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.codice', 'ASC');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @return Pagerfanta Returns an array of Articoli objects
     */
    public function findByCodice(int $page = 1, string $codice) : Pagerfanta
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.codice like :val')
            ->orderBy('a.codice', 'ASC')
            ->setParameter('val', '%'.$codice.'%');

        return $this->createPaginator($qb->getQuery(), $page);
    }


    // /**
    //  * @return Articoli[] Returns an array of Articoli objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Articoli
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Articoli::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}

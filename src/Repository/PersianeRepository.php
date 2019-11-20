<?php

namespace App\Repository;

use App\Entity\Persiane;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Persiane|null find($id, $lockMode = null, $lockVersion = null)
 * @method Persiane|null findOneBy(array $criteria, array $orderBy = null)
 * @method Persiane[]    findAll()
 * @method Persiane[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersianeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Persiane::class);
    }

    /**
     * @return Pagerfanta
     */
    public function listAll(int $page=1) :Pagerfanta
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.codice', 'ASC');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @return Pagerfanta Returns an array of Articoli objects
     */
    public function findByCodice(int $page = 1, string $codice) : Pagerfanta
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.codice like :val')
            ->orderBy('p.codice', 'ASC')
            ->setParameter('val', '%'.$codice.'%');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Persiane::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    // /**
    //  * @return Persiane[] Returns an array of Persiane objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Persiane
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

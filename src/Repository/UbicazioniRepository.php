<?php

namespace App\Repository;

use App\Entity\Ubicazioni;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ubicazioni|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ubicazioni|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ubicazioni[]    findAll()
 * @method Ubicazioni[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UbicazioniRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ubicazioni::class);
    }

    /**
     * @return Pagerfanta
     */
    public function all(int $page=1) :Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('a')
            ->leftJoin('u.articolo', 'a')
            ->orderBy('u.fila', 'ASC')
            ->addOrderBy('u.colonna', 'ASC')
            ->addOrderBy('u.piano', 'ASC');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    public function findUbicazioniFila(int $fila=1)
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('a')
            ->leftJoin('u.articolo', 'a')
            ->where('u.fila = :fila')
            ->OrderBy('u.colonna', 'ASC')
            ->addOrderBy('u.piano', 'ASC')
            ->setParameter('fila', $fila);

        return $qb->getQuery()->getResult();
    }

    public function findUbicazioniDoppieVuote()
    {
        $sql = "select id from ubicazioni u1 where
            exists (select * from (
            select codice, count(codice) c FROM ubicazioni u2 group by codice having c >1) u3
            where u3.codice = u1.codice) and u1.articolo_id is null order by u1.codice";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @return Pagerfanta
     */
    public function findFree(int $page=1) :Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('a')
            ->leftJoin('u.articolo', 'a')
            ->where('u.articolo is null')
            ->orderBy('u.fila', 'ASC')
            ->addOrderBy('u.colonna')
            ->addOrderBy('u.piano');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @return Pagerfanta
     */
    public function findFreeFila(int $page=1, int $fila) :Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('a')
            ->leftJoin('u.articolo', 'a')
            ->where('u.articolo is null')
            ->andWhere('u.fila = :fila')
            ->orderBy('u.fila', 'ASC')
            ->addOrderBy('u.colonna')
            ->addOrderBy('u.piano')
            ->setParameter('fila' , $fila);

        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @return Pagerfanta Returns an array of Ubicazioni objects
     */
    public function findByCodice(int $page = 1, string $codice) : Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.codice like :val')
            ->orderBy('u.codice', 'ASC')
            ->setParameter('val', $codice.'%');

        return $this->createPaginator($qb->getQuery(), $page);
    }

    /**
     * @return Pagerfanta Returns an array of Ubicazioni objects
     */
    public function findByArticolo(int $page = 1, string $codice) : Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('a')
            ->leftJoin('u.articolo', 'a')
            ->where('a.codice like :val')
            ->orderBy('u.codice', 'ASC')
            ->setParameter('val', '%'.$codice.'%');

        return $this->createPaginator($qb->getQuery(), $page);
    }



    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Ubicazioni::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function elencoFile()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.fila','ASC')
            ->groupBy('u.fila')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Ubicazioni[] Returns an array of Ubicazioni objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ubicazioni
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

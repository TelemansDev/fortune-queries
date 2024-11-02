<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrdered(): array
    {
        $qb = $this->addGroupByCategoryAndCountFortunes()
            ->addOrderBy('c.name', 'DESC');

        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return Category[]
//     */
//    public function searchByName(string $searchTerm): array
//    {
//        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
//        $rsm->addRootEntityFromClassMetadata(Category::class, 'c');
//
//        $sql = "SELECT * FROM category c WHERE c.name ILIKE :searchTerm ORDER BY c.name DESC";
//        $query = $this->getEntityManager()
//            ->createNativeQuery($sql, $rsm)
//            ->setParameter('searchTerm', '%' . $searchTerm . '%');
//
//        return $query->getResult();
//    }

    public function searchByName(string $searchTerm): array
    {
        $qb = $this->addGroupByCategoryAndCountFortunes()
            ->andWhere('ILIKE(c.name, :searchTerm) = TRUE')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('c.name', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findWithFortuneJoins(int $id): ?Category
    {
        return $this->createQueryBuilder('c')
            ->addSelect('fc')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->orderBy('RANDOM()')
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function addGroupByCategoryAndCountFortunes(QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('c'))
            ->addSelect('COUNT(fc.id) AS fortunesCookiesTotal')
            ->leftJoin('c.fortuneCookies', 'fc')
            ->andWhere('fc.discontinued = false')
            ->addGroupBy('c.id');
    }
}

<?php

namespace App\Repository;

use App\DTO\CategoryFortuneStats;
use App\Entity\Category;
use App\Entity\FortuneCookie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 *
 * @method FortuneCookie|null find($id, $lockMode = null, $lockVersion = null)
 * @method FortuneCookie|null findOneBy(array $criteria, array $orderBy = null)
 * @method FortuneCookie[]    findAll()
 * @method FortuneCookie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public static function createFortuneCookiesStillInProductionCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued', false));
    }

    public function save(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countNumberPrintedForCategory(Category $category): CategoryFortuneStats
    {
        return $this->createQueryBuilder('fc')
            ->select(sprintf(
                'NEW %s(
                    SUM(fc.numberPrinted),
                    AVG(fc.numberPrinted),
                    c.name
                )',
                CategoryFortuneStats::class
            ))
            ->join('fc.category', 'c')
            ->andWhere('fc.category = :category')
            ->groupBy('c.name')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleResult();
    }
}

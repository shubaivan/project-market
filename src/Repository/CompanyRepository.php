<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * Example custom method for filtered listing.
     */
    public function findByCriteria(
        ?string $name,
        int $page = 1,
        int $limit = 10
    ): array {
        $qb = $this->createQueryBuilder('company');

        if ($name) {
            $qb->andWhere('company.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        // Add pagination
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Count total items matching filter.
     */
    public function countByCriteria(
        ?string $name
    ): int {
        $qb = $this->createQueryBuilder('company')
            ->select('COUNT(company.id)');

        if ($name) {
            $qb->andWhere('company.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

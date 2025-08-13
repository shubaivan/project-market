<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * @param string|null $email Partial match on the email
     * @param int         $page  Current page (1-indexed)
     * @param int         $limit Items per page
     *
     * @return Employee[]
     */
    public function findByCriteria(?string $email, int $page, int $limit): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($email) {
            $qb->andWhere('e.email LIKE :email')
                ->setParameter('email', '%' . $email . '%');
        }

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('e.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string|null $email Partial match on the email
     *
     * @return int Total matching employees
     */
    public function countByCriteria(?string $email): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)');

        if ($email) {
            $qb->andWhere('e.email LIKE :email')
                ->setParameter('email', '%' . $email . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

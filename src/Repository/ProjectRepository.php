<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Example method to find projects by name and apply pagination.
     */
    public function findByCriteria(
        ?string $name,
        int $page = 1,
        int $limit = 10
    ): array {
        $qb = $this->createQueryBuilder('project');

        if ($name) {
            $qb->andWhere('project.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Count total projects matching a given name.
     */
    public function countByCriteria(
        ?string $name
    ): int {
        $qb = $this->createQueryBuilder('project')
            ->select('COUNT(project.id)');

        if ($name) {
            $qb->andWhere('project.name LIKE :name')
                ->setParameter('name', '%'.$name.'%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

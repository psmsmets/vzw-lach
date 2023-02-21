<?php

namespace App\Repository;

use App\Entity\Associate;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

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

    /**
     * @return Category[] Returns an array of Category objects
     */
    public function findByAssociate(Associate $associate): array
    {
        $qb = $this->createQueryBuilder('category');

        $qb->where($qb->expr()->isMemberOf(':associate', 'category.associates'));

        $qb->setParameter('associate', $associate->getId(), 'uuid');

        return $qb->getQuery()->getResult();

    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        if (isset($criteria['id']) && is_array($criteria['id'])) {
            $ids = [];
            foreach ($criteria['id'] as $id) {
                if (Uuid::isValid($id)) {
                    $ids[] = Uuid::fromString($id)->toBinary();
                } else {
                    $ids[] = $id;
                }
            }
            $criteria['id'] = $ids;
        }
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}

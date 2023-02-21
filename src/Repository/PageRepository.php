<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function save(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneBySlug($slug): ?Page
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
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

<?php

namespace App\Repository;

use App\Entity\Folder;
use App\Service\ProfileViewpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Folder>
 *
 * @method Folder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Folder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Folder[]    findAll()
 * @method Folder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    public function save(Folder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Folder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Folder[] Returns an array of Folder objects
     */
    public function findFolder(string $slug, $obj = null): ?Folder
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.documents','documents');
        $qb->addSelect('documents');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('slug', $slug);
        $qb->andWhere('entity.slug = :slug');

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findFolders($obj = null, $limit = null, $page = 1): array
    {
        $limit = is_null($limit) ? Folder::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Folder::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        $qb->orderBy('entity.name', 'ASC');
        $qb->setFirstResult($offset);
        //$qb->setMaxResults($limit);

        return array_slice($qb->getQuery()->getResult(), 0, $limit);
    }

    public function countFolders($obj = null): int
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        return count($qb->getQuery()->getResult());
    }

    public function findPlaylists($obj = null): array
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('playlist', true);
        $qb->andWhere('entity.playlist = :playlist');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        $qb->orderBy('entity.name', 'ASC');

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

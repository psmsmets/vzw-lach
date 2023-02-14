<?php

namespace App\Repository;

use App\Entity\Folder;
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
        $qb = $this->createQueryBuilder('folder');

        $qb->leftJoin('folder.documents','documents');
        $qb->addSelect('documents');

        $qb->leftJoin('folder.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                    $count++;
                }
            }
        }

        if ($obj instanceof Category) {
            $qb->setParameter(':category', $obj);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
            $count = 0;
            foreach ($obj->getChildren() as $child) {
                $qb->setParameter(sprintf('category%d', $count), $child->getId());
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                $count++;
            }
        }

        if ($obj instanceof User) {
            if ($obj->isViewmaster()) {
                $qb->orWhere('categories is not null');
            } else {
                $count = 0;
                foreach ($obj->getEnabledAssociates() as $associate) {
                    $qb->setParameter(sprintf('associate%d', $count), $associate->getId(), 'uuid');
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':associate%d', $count), 'categories.associates'));
                    $count++;
                }
            }
        }

        $qb->orWhere('categories is null');

        $qb->setParameter('slug', $slug);
        $qb->andWhere('folder.slug = :slug');

        $qb->setParameter('published', true);
        $qb->andWhere('folder.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('folder.publishedAt <= :now');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findFolders($obj = null, ?int $limit = null, int $page = 1): array
    {
        $limit = is_null($limit) ? Folder::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Folder::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('folder');

        $qb->leftJoin('folder.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                    $count++;
                }
            }
        }

        if ($obj instanceof Category) {
            $qb->setParameter(':category', $obj);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
            $count = 0;
            foreach ($obj->getChildren() as $child) {
                $qb->setParameter(sprintf('category%d', $count), $child->getId());
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                $count++;
            }
        }

        if ($obj instanceof User) {
            if ($obj->isViewmaster()) {
                $qb->orWhere('categories is not null');
            } else {
                $count = 0;
                foreach ($obj->getEnabledAssociates() as $associate) {
                    $qb->setParameter(sprintf('associate%d', $count), $associate->getId(), 'uuid');
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':associate%d', $count), 'categories.associates'));
                    $count++;
                }
            }
        }

        $qb->orWhere('categories is null');

        $qb->setParameter('published', true);
        $qb->andWhere('folder.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('folder.publishedAt <= :now');

        $qb->orderBy('folder.name', 'ASC');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countFolders($obj = null): int
    {
        $qb = $this->createQueryBuilder('folder');

        $qb->leftJoin('folder.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                    $count++;
                }
            }
        }

        if ($obj instanceof Category) {
            $qb->setParameter(':category', $obj);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
            $count = 0;
            foreach ($obj->getChildren() as $child) {
                $qb->setParameter(sprintf('category%d', $count), $child->getId());
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'folder.categories'));
                $count++;
            }
        }

        if ($obj instanceof User) {
            if ($obj->isViewmaster()) {
                $qb->orWhere('categories is not null');
            } else {
                $count = 0;
                foreach ($obj->getEnabledAssociates() as $associate) {
                    $qb->setParameter(sprintf('associate%d', $count), $associate->getId(), 'uuid');
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':associate%d', $count), 'categories.associates'));
                    $count++;
                }
            }
        }

        $qb->orWhere('categories is null');

        $qb->setParameter('published', true);
        $qb->andWhere('folder.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('folder.publishedAt <= :now');

        return count($qb->getQuery()->getResult());
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

<?php

namespace App\Repository;

use App\Entity\Associate;
use App\Entity\Category;
use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Document>
 *
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function save(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Document $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Document[] Returns a Document objects
     */
    public function findDocument(Uuid $uuid, $obj = null): ?Document
    {
        $qb = $this->createQueryBuilder('doc');

        $qb->leftJoin('doc.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
        $qb->andWhere('doc.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('doc.publishedAt <= :now');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('doc.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Document[] Returns an array of Document objects
     */
    public function findDocuments(
        $obj = null, ?bool $special = null, ?bool $pinned = null, ?Folder $folder = null, ?int $limit = null, int $page = 1
    ): array
    {
        $limit = is_null($limit) ? Document::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Document::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('doc');

        $qb->leftJoin('doc.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
        $qb->andWhere('doc.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('doc.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('doc.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('doc.pinned = :pinned');
        }

        $qb->orderBy('doc.name', 'ASC');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countDocuments(
        $obj = null, ?bool $special = null, ?bool $pinned = null, ?Folder $folder = null
    ): int
    {
        $qb = $this->createQueryBuilder('doc');

        $qb->leftJoin('doc.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
            $count = 0;
            foreach ($obj->getCategories() as $category) {
                foreach ($category->getChildren() as $child) {
                    $qb->setParameter(sprintf('category%d', $count), $child->getId());
                    $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
                $qb->orWhere($qb->expr()->isMemberOf(sprintf(':category%d', $count), 'doc.categories'));
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
        $qb->andWhere('doc.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('doc.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('doc.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('doc.pinned = :pinned');
        }

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

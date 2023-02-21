<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Folder;
use App\Service\ProfileViewpoint;
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
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('entity.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Document[] Returns an array of Document objects
     */
    public function findDocuments(
        $folder = null, $obj = null, $special = null, $pinned = null, $limit = null, $page = 1
    ): array
    {
        $limit = is_null($limit) ? Document::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Document::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        if ($folder instanceof Folder) {
            $qb->leftJoin('entity.folder','folder');
            $qb->addSelect('folder');
            $qb->setParameter('folder', $folder);
            $qb->andWhere('entity.folder = :folder');
        }

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('entity.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('entity.pinned = :pinned');
        }

        $qb->orderBy('entity.name', 'ASC');
        $qb->setFirstResult($offset);
        //$qb->setMaxResults($limit);

        return array_slice($qb->getQuery()->getResult(), 0, $limit);
    }

    public function countDocuments(
        $folder = null, $obj = null, $special = null, $pinned = null
    ): int
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');
        ProfileViewpoint::categoriesFilter($qb, $obj);

        if ($folder instanceof Folder) {
            $qb->leftJoin('entity.folder','folder');
            $qb->addSelect('folder');
            $qb->setParameter('folder', $folder);
            $qb->andWhere('entity.folder = :folder');
        }

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        if (!is_null($special)) {
            $qb->setParameter('special', $special);
            $qb->andWhere('entity.special = :special');
        }

        if (!is_null($pinned)) {
            $qb->setParameter('pinned', $pinned);
            $qb->andWhere('entity.pinned = :pinned');
        }

        return count($qb->getQuery()->getResult());
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

<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Associate;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function save(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Advert[] Returns a Advert objects
     */
    public function findAdvert(Uuid $uuid): ?Advert
    {
        $qb = $this->createQueryBuilder('ad');

        $qb->setParameter('published', true);
        $qb->andWhere('ad.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('ad.publishedAt <= :now');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('ad.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Advert[] Returns an array of Advert objects
     */
    public function findAdverts(
        $limit = null, $page = 1, $progress = null, ?bool $completed = null, ?int $tag = null, bool $shuffle = false
    ): array
    {
        $limit = is_null($limit) ? Advert::NUMBER_OF_ITEMS : $limit;
        $offset = ( $page < 1 ? 0 : $page - 1 ) * Advert::NUMBER_OF_ITEMS;

        $qb = $this->createQueryBuilder('ad');

        $qb->setParameter('published', true);
        $qb->andWhere('ad.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('ad.publishedAt <= :now');

        if (!is_null($progress)) {
            $qb->setParameter('progress', $progress);
            $qb->andWhere('ad.progress < :progress');
        }

        if (!is_null($completed)) {
            $qb->setParameter('completed', $completed);
            $qb->andWhere('ad.completed = :completed');
        }

        if (!is_null($tag) and $tag > 0) {
            $qb->setParameter('tag', $tag);
            $qb->andWhere($qb->expr()->isMemberOf(':tag', 'ad.tags'));
        }

        if ($shuffle) {
            $result = $qb->getQuery()->getResult();
            shuffle($result);
        } else {
            $qb->addOrderBy('ad.progress', 'ASC');
            $qb->addOrderBy('ad.publishedAt', 'DESC');
            $qb->setMaxResults($limit);
            $qb->setFirstResult($offset);
            $result = $qb->getQuery()->getResult();
        }

        return $shuffle ? array_slice($result, 0, $limit) : $result;
    }

    public function countAdverts(?int $tag = null): int
    {
        $qb = $this->createQueryBuilder('ad');

        $qb->setParameter('published', true);
        $qb->andWhere('ad.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('ad.publishedAt <= :now');

        if (!is_null($tag) or $tag > 0) {
            $qb->setParameter('tag', $tag);
            $qb->andWhere($qb->expr()->isMemberOf(':tag', 'ad.tags'));
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

<?php

namespace App\Repository;

use App\Entity\Event;
use App\Service\ProfileViewpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findEvent(Uuid $uuid, $obj = null): ?Event
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');

        $qb->leftJoin('categories.associates','associates');
        $qb->addSelect('associates');

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
     * @return Event[] Returns an array of Event objects
     */
    public function findEvents(
        $obj = null, ?\DateTimeInterface $periodStart = null, ?\DateTimeInterface $periodEnd = null, $limit = null
    ): array
    {
        $t0 = is_null($periodStart) ? new \DateTime('today midnight') : $periodStart;
        $t1 = $periodEnd;

        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.categories','categories');
        $qb->addSelect('categories');

        $qb->leftJoin('categories.associates','associates');
        $qb->addSelect('associates');

        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('published', true);
        $qb->andWhere('entity.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        if (is_null($t1)) {
            $qb->setParameter('t0', $t0);
            $qb->andWhere('( entity.endTime >= :t0 or (entity.startTime >= :t0 and entity.endTime is null) )');
        } else {
            $qb->setParameter('t0', $t0);
            $qb->setParameter('t1', $t1);
            $qb->andWhere('entity.startTime >= :t0');
            $qb->andWhere('(entity.endTime < :t1 or (entity.endTime is null and entity.startTime < :t1))');
        }

        $qb->orderBy('entity.startTime', 'ASC');
        //$qb->setMaxResults($limit);

        return array_slice($qb->getQuery()->getResult(), 0, $limit);
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

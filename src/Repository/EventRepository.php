<?php

namespace App\Repository;

use App\Entity\Associate;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\User;
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
        $qb = $this->createQueryBuilder('event');

        $qb->leftJoin('event.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
        }

        if ($obj instanceof Category) {
            $qb->setParameter(':category', $obj);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
        }

        if ($obj instanceof User) {
            if  ($obj->isViewmaster()) {
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
        $qb->andWhere('event.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('event.publishedAt <= :now');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('event.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findEvents(
        $obj = null, ?\DateTimeInterface $periodStart = null, ?\DateTimeInterface $periodEnd = null, ?int $limit = null
    ): array
    {
        $t0 = is_null($periodStart) ? new \DateTime('today midnight') : $periodStart;
        $t1 = $periodEnd;

        $qb = $this->createQueryBuilder('event');

        $qb->leftJoin('event.categories','categories');
        $qb->addSelect('categories');

        if ($obj instanceof Associate) {
            $qb->setParameter('associate', $obj->getId(), 'uuid');
            $qb->where($qb->expr()->isMemberOf(':associate', 'categories.associates'));
        }

        if ($obj instanceof Category) {
            $qb->setParameter(':category', $obj);
            $qb->where($qb->expr()->isMemberOf(':category', 'categories'));
        }

        if ($obj instanceof User) {
            if  ($obj->isViewmaster()) {
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
        $qb->andWhere('event.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('event.publishedAt <= :now');

        if (is_null($t1)) {
            $qb->setParameter('t0', $t0);
            $qb->andWhere('( event.endTime >= :t0 or (event.startTime >= :t0 and event.endTime is null) )');
        } else {
            $qb->setParameter('t0', $t0);
            $qb->setParameter('t1', $t1);
            $qb->andWhere('event.startTime >= :t0');
            $qb->andWhere('(event.endTime < :t1 or (event.endTime is null and event.startTime < :t1))');
        }

        $qb->orderBy('event.startTime', 'ASC');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
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

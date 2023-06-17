<?php

namespace App\Repository;

use App\Entity\Enrolment;
use App\Entity\Event;
use App\Service\ProfileViewpoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Enrolment>
 *
 * @method Enrolment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enrolment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enrolment[]    findAll()
 * @method Enrolment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnrolmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrolment::class);
    }

    public function save(Enrolment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Enrolment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Enrolment[] Returns an array of Enrolment objects
     */
    public function findEnrolment(Uuid $uuid): ?Enrolment
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.event','event');
        $qb->addSelect('event');

        $qb->setParameter('uuid', $uuid, 'uuid');
        $qb->andWhere('entity.id = :uuid');

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Enrolment[] Returns an array of Enrolment objects
     */
    public function findEnrolments(
        $obj = null, $event = null, $limit = null, $tag = null
    ): array
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->leftJoin('entity.event','event');
        $qb->addSelect('event');

        $qb->leftJoin('event.categories','categories');
        $qb->addSelect('categories');

        $qb->leftJoin('categories.associates','associates');
        $qb->addSelect('associates');

        ProfileViewpoint::categoriesFilter($qb, $obj);

        $qb->setParameter('event.published', true);
        $qb->andWhere('event.published = :published');

        $qb->setParameter('now', new \DateTime());
        $qb->andWhere('entity.publishedAt <= :now');

        if (!is_null($event) ) {
            $qb->setParameter('uuid', $event->getId(), 'uuid');
            $qb->andWhere('event.id = :uuid');
        }

        $qb->orderBy('event.startTime', 'ASC');
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

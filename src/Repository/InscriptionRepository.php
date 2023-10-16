<?php

namespace AcMarche\Sport\Repository;

use AcMarche\Sport\Doctrine\OrmCrudTrait;
use AcMarche\Sport\Entity\Activity;
use AcMarche\Sport\Entity\Inscription;
use AcMarche\Sport\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inscription>
 *
 * @method Inscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscription[]    findAll()
 * @method Inscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscription::class);
    }

    public function findAllOrdered(): array
    {
        return $this->createQbl()
            ->orderBy('inscription.id', 'ASC')->getQuery()
            ->getResult();
    }

    /**
     * @param Person $person
     * @return Inscription[]
     */
    public function findByPerson(Person $person): array
    {
        return $this->createQbl()
            ->andWhere('inscription.person = :person')
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();
    }
    /**
     * @param Person $person
     * @return Inscription[]
     */
    public function findByPersonAndValidated(Person $person): array
    {
        return $this->createQbl()
            ->andWhere('inscription.person = :person')
            ->setParameter('person', $person)
            ->andWhere('inscription.validated = 1')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Activity $activity
     * @return Inscription[]
     */
    public function findByActivity(Activity $activity): array
    {
        return $this->createQbl()
            ->andWhere('inscription.activity = :activity')
            ->addOrderBy('inscription.preference_number', 'ASC')
            ->setParameter('activity', $activity)
            ->addOrderBy('inscription.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Activity $activity
     * @return Inscription[]
     */
    public function findValidatedByActivity(Activity $activity): array
    {
        return $this->createQbl()
            ->andWhere('inscription.activity = :activity')
            ->setParameter('activity', $activity)
            ->andWhere('inscription.validated = 1')
            ->addOrderBy('person.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Activity $activity
     * @return Inscription[]
     */
    public function findNotValidatedByActivity(Activity $activity): array
    {
        return $this->createQbl()
            ->andWhere('inscription.activity = :activity')
            ->setParameter('activity', $activity)
            ->andWhere('inscription.validated = 0')
            ->addOrderBy('inscription.preference_number', 'ASC')
            ->addOrderBy('inscription.createdAt', 'ASC')
            ->addOrderBy('person.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByPersonAndActivity(Person $person, Activity $activity): ?Inscription
    {
        return $this->createQbl()
            ->andWhere('inscription.person = :person')
            ->setParameter('person', $person)
            ->andWhere('inscription.activity = :activity')
            ->setParameter('activity', $activity)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createQbl(): QueryBuilder
    {
        return $this->createQueryBuilder('inscription')
            ->leftJoin('inscription.person', 'person', 'WITH')
            ->leftJoin('inscription.activity', 'activity', 'WITH')
            ->addSelect('person', 'activity');
    }
}

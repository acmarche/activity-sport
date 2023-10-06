<?php

namespace AcMarche\Sport\Repository;

use AcMarche\Sport\Doctrine\OrmCrudTrait;
use AcMarche\Sport\Entity\Activity;
use AcMarche\Sport\Entity\Inscription;
use AcMarche\Sport\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        return $this->createQueryBuilder('inscription')
            ->orderBy('inscription.id', 'ASC')->getQuery()
            ->getResult();
    }

    /**
     * @param Person $person
     * @return Inscription[]
     */
    public function findByPerson(Person $person): array
    {
        return $this->createQueryBuilder('inscription')
            ->andWhere('inscription.person = :person')
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Activity $activity
     * @return Inscription[]
     */
    public function findByActivity(Activity $activity): array
    {
        return $this->createQueryBuilder('inscription')
            ->andWhere('inscription.activity = :activity')
            ->setParameter('activity', $activity)
            ->getQuery()
            ->getResult();
    }

    public function findOneByPersonAndActivity(Person $person, Activity $activity): ?Inscription
    {
        return $this->createQueryBuilder('inscription')
            ->andWhere('inscription.person = :person')
            ->setParameter('person', $person)
            ->andWhere('inscription.activity = :activity')
            ->setParameter('activity', $activity)
            ->getQuery()
            ->getOneOrNullResult();
    }

}

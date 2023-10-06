<?php

namespace AcMarche\Sport\Repository;

use AcMarche\Sport\Doctrine\OrmCrudTrait;
use AcMarche\Sport\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Person>
 *
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('person')
            ->orderBy('person.name', 'ASC')->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?Person
    {
        return $this->createQueryBuilder('person')
            ->andWhere('person.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

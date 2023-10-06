<?php

namespace AcMarche\Sport\Entity;

use AcMarche\Sport\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
#[ORM\UniqueConstraint(columns: ['person_id', 'activity_id'])]
#[UniqueEntity(fields: ['person', 'activity'], message: 'Déjà inscrit à cette activité')]
class Inscription implements TimestampableInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(type: 'smallint')]
    public int $preferenceNumber;

    #[ORM\ManyToOne(targetEntity: Person::class)]
    public Person $person;

    #[ORM\ManyToOne(targetEntity: Activity::class)]
    public Activity $activity;

    public function __construct(Person $person, Activity $activity)
    {
        $this->person = $person;
        $this->activity = $activity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}

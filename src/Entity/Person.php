<?php

namespace AcMarche\Sport\Entity;

use AcMarche\Sport\Repository\PersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\Table(name: 'person')]
class Person
{
    use UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 150)]
    public ?string $name = null;

    #[ORM\Column(length: 150)]
    public ?string $surname = null;

    #[ORM\Column(length: 150, unique: true)]
    public ?string $email = null;

    /**
     * @var Inscription[]
     */
    public array $inscriptions = [];
    /**
     * @var Inscription[]
     */
    public array $inscriptionsValidated = [];

    public function __toString(): string
    {
        return $this->surname.' '.$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}

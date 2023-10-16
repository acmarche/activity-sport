<?php

namespace AcMarche\Sport\Entity;

use AcMarche\Sport\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'activity')]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT)]
    public ?int $max_participant = null;

    public array $inscriptionsValidated = [];

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaxParticipant(): ?int
    {
        return $this->max_participant;
    }

    public function getName(): string
    {
        return $this->name;
    }


}

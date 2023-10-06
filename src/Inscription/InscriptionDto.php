<?php

namespace AcMarche\Sport\Inscription;

use AcMarche\Sport\Entity\Person;

class InscriptionDto
{
    public Person $person;
    public array $selections = [];

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

}
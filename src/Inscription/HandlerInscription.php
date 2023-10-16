<?php

namespace AcMarche\Sport\Inscription;

use AcMarche\Sport\Entity\Activity;
use AcMarche\Sport\Entity\Inscription;
use AcMarche\Sport\Entity\Person;
use AcMarche\Sport\Repository\ActivityRepository;
use AcMarche\Sport\Repository\InscriptionRepository;

class HandlerInscription
{
    public function __construct(
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly ActivityRepository $activityRepository
    ) {
    }

    /**
     * @throws \Exception
     */
    public function check(array $selections): array
    {
        unset($selections['_token']);
        $choices = [];

        foreach ($selections as $activityId => $number) {
            if ((int)$number > 0) {
                if (!$this->checkNumber((int)$number)) {
                    throw new \Exception('le numéro doit être entre 1 et 4'.(int)$number);
                }
                $choices[$activityId] = $number;
            }
        }

        if (count($choices) > 4) {
            throw new \Exception('Ne choisissez que 4 activités, vous en avez choisit '.count($choices));
        }

        $dupes = $this->checkDuplicate($choices);
        if (count($dupes)) {
            throw new \Exception('Les préférences suivantes ont la même valeur: '.join(',', $dupes));
        }

        return $choices;
    }

    private function checkNumber(int $number): bool
    {
        return $number > 0 && $number < 5;
    }

    public function init(Person $person): InscriptionDto
    {
        $inscriptionDto = new InscriptionDto($person);
        $inscriptions = $this->inscriptionRepository->findByPerson($person);
        $choices = [];
        foreach ($inscriptions as $inscription) {
            $choices[$inscription->activity->id] = $inscription;
        }
        $inscriptionDto->selections = $choices;

        return $inscriptionDto;
    }

    private function checkNumber1234(array $choices): bool
    {
        $choicesOk = [1, 2, 3, 4];
        $choicesSelected = [];
        foreach ($choices as $number) {
            $choicesSelected[] = $number;
        }

        return count(array_diff($choicesOk, $choicesSelected)) > 0;
    }

    private function checkDuplicate(array $choices): array
    {
        $unique = array_unique($choices);

        return array_diff_key($choices, $unique);
    }

    /**
     * @param Person $person
     * @param array $selections
     * @return Inscription[]
     */
    public function treatment(Person $person, array $selections): array
    {
        $inscriptions = [];
        $this->removeOldInscription($person, $selections);
        foreach ($selections as $activityId => $number) {
            $activity = $this->activityRepository->find($activityId);
            if (!$inscription = $this->inscriptionRepository->findOneByPersonAndActivity($person, $activity)) {
                $inscription = new Inscription($person, $activity);
                $this->inscriptionRepository->persist($inscription);
            }
            $inscription->preference_number = $number;
            $inscriptions[] = $inscription;
        }
        $this->inscriptionRepository->flush();

        return $inscriptions;
    }

    private function removeOldInscription(Person $person, array $selections)
    {
        $inscriptions = $this->inscriptionRepository->findByPerson($person);
        $currentActivities = $newActivities = [];
        foreach ($inscriptions as $inscription) {
            $currentActivities[$inscription->getId()] = $inscription->activity->getId();
        }

        foreach ($selections as $activityId => $number) {
            $newActivities[] = $activityId;
        }

        $spEnMoins = array_diff($currentActivities, $newActivities);

        foreach ($spEnMoins as $idInscription => $activityId) {
            $inscription = $this->inscriptionRepository->find($idInscription);
            $this->inscriptionRepository->remove($inscription);
        }
    }

    public function treatmentValidate(Activity $activity, Inscription $inscription, string $action): array
    {
        if ($action === 'add') {
            $inscription->validated = true;
        } else {
            $inscription->validated = false;
        }
        $this->inscriptionRepository->flush();

        return $this->inscriptionRepository->findValidatedByActivity($activity);
    }
}
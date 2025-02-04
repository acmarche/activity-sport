<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Entity\Person;
use AcMarche\Sport\Form\PersonType;
use AcMarche\Sport\Repository\InscriptionRepository;
use AcMarche\Sport\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/person')]
class PersonController extends AbstractController
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly InscriptionRepository $inscriptionRepository
    ) {
    }

    #[Route(path: '/', name: 'sport_admin_person', methods: ['GET'])]
    #[IsGranted('ROLE_SPORT_ADMIN')]
    public function index(Request $request): Response
    {
        $persons = $this->personRepository->findAllOrdered();
        foreach ($persons as $person) {
            $person->inscriptions = $this->inscriptionRepository->findByPerson($person);
            $person->inscriptionsValidated = $this->inscriptionRepository->findByPersonAndValidated($person);
        }

        return $this->render(
            '@AcMarcheSport/person/index.html.twig',
            [
                'persons' => $persons,
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'sport_admin_person_show', methods: ['GET'])]
    public function show(Person $person): Response
    {
        $inscriptions = $this->inscriptionRepository->findByPerson($person);
        $inscriptionsValidated = $this->inscriptionRepository->findByPersonAndValidated($person);

        return $this->render(
            '@AcMarcheSport/person/show.html.twig',
            [
                'person' => $person,
                'inscriptions' => $inscriptions,
                'inscriptionsValidated' => $inscriptionsValidated,
            ]
        );
    }

    #[Route(path: '/{uuid}/edit', name: 'sport_admin_person_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SPORT_ADMIN')]
    public function edit(Person $person, Request $request): Response
    {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->addFlash('success', 'Participant modifié');
            $this->personRepository->flush();

            return $this->redirectToRoute('sport_admin_person_show', ['uuid' => $person->uuid]);
        }

        return $this->render(
            '@AcMarcheSport/person/edit.html.twig',
            [
                'person' => $person,
                'form' => $editForm,
            ]
        );
    }

    #[Route(path: '/{uuid}', name: 'sport_admin_person_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SPORT_ADMIN')]
    public function delete(Request $request, Person $person): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$person->uuid, $request->request->get('_token'))) {

            foreach ($this->inscriptionRepository->findByPerson($person) as $inscription) {
                $this->inscriptionRepository->remove($inscription);
            }
            $this->personRepository->remove($person);
            $this->personRepository->flush();

            $this->addFlash('success', 'Le participant a bien été supprimé');
        }

        return $this->redirectToRoute('sport_home');
    }
}

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
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/person')]
class PersonController extends AbstractController
{
    public function __construct(
        private readonly PersonRepository $personRepository,
        private readonly InscriptionRepository $inscriptionRepository
    ) {
    }

    #[Route(path: '/', name: 'sport_admin_person', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $persons = $this->personRepository->findAllOrdered();
        foreach ($persons as $person) {
            $person->inscriptions = $this->inscriptionRepository->findByPerson($person);
        }

        return $this->render(
            '@AcMarcheSport/person/index.html.twig',
            [
                'persons' => $persons,
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_admin_person_show', methods: ['GET'])]
    public function show(Person $person): Response
    {
        $inscriptions = $this->inscriptionRepository->findByPerson($person);

        return $this->render(
            '@AcMarcheSport/person/show.html.twig',
            [
                'person' => $person,
                'inscriptions' => $inscriptions,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'sport_admin_person_edit', methods: ['GET', 'POST'])]
    public function edit(Person $person, Request $request): Response
    {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->personRepository->flush();

            return $this->redirectToRoute('sport_admin_person_show', ['id' => $person->getId()]);
        }

        return $this->render(
            '@AcMarcheSport/person/edit.html.twig',
            [
                'person' => $person,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_admin_person_delete', methods: ['POST'])]
    public function delete(Request $request, Person $person): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $this->personRepository->remove($person);
            $this->personRepository->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée');

            return $this->redirectToRoute('sport_admin_person_show', ['id' => $person->getId()]);

        }

        return $this->redirectToRoute('sport_admin_person');
    }
}

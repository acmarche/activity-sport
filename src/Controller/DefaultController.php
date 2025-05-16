<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Form\ContactType;
use AcMarche\Sport\Form\SearchSimpleType;
use AcMarche\Sport\Mailer\MailerSport;
use AcMarche\Sport\Repository\InscriptionRepository;
use AcMarche\Sport\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    public function __construct(
        private readonly MailerSport $mailerSport,
        private readonly PersonRepository $personRepository,
        private readonly InscriptionRepository $inscriptionRepository
    ) {
    }

    #[Route(path: '/', name: 'sport_home')]
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheSport/default/index.html.twig',
            [

            ]
        );
    }

    #[Route(path: '/search/form', name: 'sport_search_form')]
    public function searchForm(): Response
    {
        $form = $this->createForm(
            SearchSimpleType::class,
            [],
            [
                'method' => 'GET',
                'action' => $this->generateUrl('sport_search_form'),
            ]
        );

        return $this->render(
            '@AcMarcheSport/default/_search_form.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    #[Route(path: '/contact', name: 'sport_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $persons = $this->personRepository->findAllOrdered();
            $data = $form->getData();
            foreach ($persons as $person) {
                $inscriptionsValidated = $this->inscriptionRepository->findByPersonAndValidated($person);
                try {
                    $this->mailerSport->sendAll($person, $data['subject'], $data['message'], $inscriptionsValidated);
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', 'Erreur d\'envoi pour '.$person->email.' '.$e->getMessage());
                }
            }
            $this->addFlash('success', 'Messages envoyés');

            return $this->redirectToRoute('sport_contact');
        }

        return $this->render(
            '@AcMarcheSport/default/contact.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
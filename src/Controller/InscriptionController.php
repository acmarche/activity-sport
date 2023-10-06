<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Entity\Person;
use AcMarche\Sport\Form\InscriptionType;
use AcMarche\Sport\Form\PersonType;
use AcMarche\Sport\Inscription\HandlerInscription;
use AcMarche\Sport\Mailer\MailerSport;
use AcMarche\Sport\Repository\ActivityRepository;
use AcMarche\Sport\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/inscription')]
class InscriptionController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly PersonRepository $personRepository,
        private readonly HandlerInscription $handlerInscription,
        private readonly MailerSport $mailerSport
    ) {
    }

    #[Route(path: '/', name: 'sport_inscription_new')]
    public function new(Request $request): Response
    {
        $person = new Person();

        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$this->personRepository->findOneByEmail($person->email)) {
                $this->personRepository->persist($person);
                $this->personRepository->flush();
            }

            return $this->redirectToRoute('sport_inscription_selection', ['id' => $person->getId()]);
        }

        return $this->render(
            '@AcMarcheSport/inscription/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/selection/{id}', name: 'sport_inscription_selection')]
    public function selection(Request $request, Person $person): Response
    {
        $activities = $this->activityRepository->findAllOrdered();

        $inscriptionDto = $this->handlerInscription->init($person);

        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selections = $request->get('inscription');

            try {
                $choices = $this->handlerInscription->check($selections);
                $inscriptions = $this->handlerInscription->treatment($person, $choices);
                $this->mailerSport->send($person, $inscriptions);
                $this->addFlash('success', 'Vous êtes bien inscrit aux activités');

            } catch (\Exception|TransportExceptionInterface  $exception) {
                $this->addFlash('danger', $exception->getMessage());

                return $this->redirectToRoute('sport_inscription_selection', ['id' => $person->getId()]);
            }

            return $this->redirectToRoute('sport_home');
        }

        return $this->render(
            '@AcMarcheSport/inscription/selection.html.twig',
            [
                'form' => $form->createView(),
                'person' => $person,
                'inscriptionDto' => $inscriptionDto,
                'activities' => $activities,
            ]
        );
    }
}
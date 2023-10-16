<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Entity\Activity;
use AcMarche\Sport\Form\ActivityType;
use AcMarche\Sport\Repository\ActivityRepository;
use AcMarche\Sport\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/activity')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly InscriptionRepository $inscriptionRepository
    ) {
    }

    #[Route(path: '/', name: 'sport_admin_activity', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $activities = $this->activityRepository->findAllOrdered();
        foreach ($activities as $activity) {
           $activity->inscriptionsValidated = $this->inscriptionRepository->findValidatedByActivity($activity);
        }

        return $this->render(
            '@AcMarcheSport/activity/index.html.twig',
            [
                'activities' => $activities,
            ]
        );
    }

    #[Route(path: '/new', name: 'sport_admin_activity_new')]
    #[IsGranted('ROLE_SPORT_ADMIN')]
    public function new(Request $request): Response
    {
        $activity = new Activity();

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->activityRepository->persist($activity);
            $this->activityRepository->flush();

            return $this->redirectToRoute('sport_admin_activity_show', ['id' => $activity->getId()]);
        }

        return $this->render(
            '@AcMarcheSport/activity/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_admin_activity_show', methods: ['GET'])]
    public function show(Activity $activity): Response
    {
        $inscriptions = $this->inscriptionRepository->findByActivity($activity);
        $inscriptionsValidated = $this->inscriptionRepository->findValidatedByActivity($activity);
        $inscriptionsNotValidated = $this->inscriptionRepository->findNotValidatedByActivity($activity);

        return $this->render(
            '@AcMarcheSport/activity/show.html.twig',
            [
                'activity' => $activity,
                'inscriptions' => $inscriptions,
                'inscriptionsValidated' => $inscriptionsValidated,
                'inscriptionsNotValidated' => $inscriptionsNotValidated,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'sport_admin_activity_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SPORT_ADMIN')]
    public function edit(Activity $activity, Request $request): Response
    {
        $editForm = $this->createForm(ActivityType::class, $activity);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->activityRepository->flush();


            return $this->redirectToRoute('sport_admin_activity_show', ['id' => $activity->getId()]);
        }

        return $this->render(
            '@AcMarcheSport/activity/edit.html.twig',
            [
                'activity' => $activity,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_admin_activity_delete', methods: ['POST'])]
    public function delete(Request $request, Activity $activity): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {
            $this->activityRepository->remove($activity);
            $this->activityRepository->flush();

            $this->addFlash('success', 'La catégorie a bien été supprimée');

            return $this->redirectToRoute('sport_admin_activity_show', ['id' => $activity->getId()]);

        }

        return $this->redirectToRoute('sport_admin_activity');
    }
}

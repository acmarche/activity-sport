<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Form\SearchSimpleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
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
                'form' => $form->createView(),
            ]
        );
    }
}
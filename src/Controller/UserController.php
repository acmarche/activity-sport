<?php

namespace AcMarche\Sport\Controller;

use AcMarche\Sport\Entity\User;
use AcMarche\Sport\Form\UserEditType;
use AcMarche\Sport\Form\UserPasswordType;
use AcMarche\Sport\Form\UserType;
use AcMarche\Sport\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/user')]
#[IsGranted('ROLE_SPORT_ADMIN')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    #[Route(path: '/', name: 'sport_user', methods: ['GET'])]
    public function index(): Response
    {
        $users = $this->userRepository->findBy([], ['name' => 'ASC']);

        return $this->render(
            '@AcMarcheSport/user/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    #[Route(path: '/new', name: 'sport_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, $form->getData()->getPlainPassword())
            );
            $this->userRepository->insert($user);

            $this->addFlash('success', "L'utilisateur a bien été ajouté");

            return $this->redirectToRoute('sport_user');
        }

        return $this->render(
            '@AcMarcheSport/user/new.html.twig',
            [
                'utilisateur' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render(
            '@AcMarcheSport/user/show.html.twig',
            [
                'utilisateur' => $user,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'sport_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $editForm = $this->createForm(UserEditType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->userRepository->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('sport_user');
        }

        return $this->render(
            '@AcMarcheSport/user/edit.html.twig',
            [
                'utilisateur' => $user,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/password/{id}', name: 'sport_user_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->userPasswordHasher->hashPassword($user, $form->getData()->getPlainPassword());
            $user->setPassword($password);
            $this->userRepository->flush();

            $this->addFlash('success', 'Mot de passe changé');

            return $this->redirectToRoute('sport_user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            '@AcMarcheSport/user/password.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'sport_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user);
            $this->userRepository->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé');
        }

        return $this->redirectToRoute('sport_user');
    }
}

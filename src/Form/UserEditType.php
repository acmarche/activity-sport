<?php

namespace AcMarche\Sport\Form;

use AcMarche\Sport\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    const ROLES = ['ROLE_SPORT_ADMIN', 'ROLE_SPORT'];

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $roles = self::ROLES;
        $formBuilder
            ->remove('plainPassword')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => array_combine($roles, $roles),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function getParent(): ?string
    {
        return UserType::class;
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}

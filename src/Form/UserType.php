<?php

namespace AcMarche\Sport\Form;

use AcMarche\Sport\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'surname',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add(
                'plainPassword',
                TextType::class,
                [
                    'label' => 'Mot de passe',
                ]
            );
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

<?php

namespace AcMarche\Sport\Form;

use AcMarche\Sport\Repository\ActivityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldsActivitiesSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ActivityRepository $activityRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event): void
    {
        $fiche = $event->getData();
        $form = $event->getForm();

        $form
            ->add(
                'metas',
                CollectionType::class,
                [
                    'entry_type' => MetaDataType::class,
                ]
            );
    }
}

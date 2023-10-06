<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'doctrine',
        [
            'orm' => [
                'mappings' => [
                    'AcMarche\Sport' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/AcMarche/Sport/src/Entity',
                        'prefix' => 'AcMarche\Sport',
                        'alias' => 'AcMarche\Sport',
                    ],
                ],
            ],
        ]
    );
};

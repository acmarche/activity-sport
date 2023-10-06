<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension(
        'vich_uploader',
        [
            'mappings' => [
                'bottin_fiche_image' => [
                    'uri_prefix' => '/bottin/fiches',
                    'upload_destination' => '%kernel.project_dir%/public/bottin/fiches',
                    'directory_namer' => ['service' => DirectoryNamer::class],
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
                'bottin_category_logo' => [
                    'uri_prefix' => '/bottin/categories',
                    'upload_destination' => '%kernel.project_dir%/public/bottin/categories',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
                'bottin_fiche_document' => [
                    'uri_prefix' => '/bottin/documents',
                    'upload_destination' => '%kernel.project_dir%/public/bottin/documents',
                    'namer' => 'vich_uploader.namer_uniqid',
                    'inject_on_load' => false,
                ],
            ],
        ]
    );
};

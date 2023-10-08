<?php

use Symfony\Config\DoctrineConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\Env;

return static function (DoctrineConfig $doctrine): void {
    $doctrine->dbal()
        ->connection('sport')
        ->url(env('DATABASE_SPORT_URL')->resolve())
        ->charset('utf8mb4');

    $emMda = $doctrine->orm()->entityManager('sport');
    $emMda->connection('sport');
    $emMda->mapping('AcMarcheSport')
        ->isBundle(false)
        ->type('attribute')
        ->dir('%kernel.project_dir%/src/AcMarche/Sport/src/Entity')
        ->prefix('AcMarche\Sport')
        ->alias('AcMarcheSport');

};

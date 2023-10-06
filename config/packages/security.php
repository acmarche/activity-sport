<?php

use AcMarche\Sport\Entity\User;
use AcMarche\Sport\Security\SportAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'password_hashers' => [
            User::class => ['algorithm' => 'auto'],
        ],
    ]);

    $containerConfigurator->extension(
        'security',
        [
            'providers' => [
                'sport_user_provider' => [
                    'entity' => [
                        'class' => User::class,
                        'property' => 'username',
                    ],
                ],
            ],
        ]
    );

    $authenticators = [SportAuthenticator::class];

    $main = [
        'provider' => 'sport_user_provider',
        'logout' => ['path' => 'app_logout'],
        'form_login' => [],
        'entry_point' => SportAuthenticator::class,
        'login_throttling' => [
            'max_attempts' => 6, // per minute...
        ],
    ];

    if (interface_exists(LdapInterface::class)) {
        $authenticators[] = SportLdapAuthenticator::class;
        $main['form_login_ldap'] = [
            'service' => Ldap::class,
            'check_path' => 'app_login',
        ];
    }

    $main['custom_authenticator'] = $authenticators;

    $containerConfigurator->extension(
        'security',
        [
            'firewalls' => [
                'main' => $main,
            ],
        ]
    );
};

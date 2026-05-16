<?php

declare(strict_types=1);

return [
    'provider' => [
        'label' => 'Gebruik een passkey',
    ],

    'management' => [
        'heading' => 'Passkeys',
        'description' => 'Met passkeys log je in met je vingerafdruk, gezicht of apparaat-PIN.',
        'enabled' => 'Ingeschakeld',
        'disabled' => 'Uitgeschakeld',
        'empty' => 'Nog geen passkeys geregistreerd.',
        'add' => 'Passkey toevoegen',
        'waiting' => 'Wachten op apparaat…',
        'default_name' => 'Passkey',
        'rename' => 'Hernoemen',
        'renamed' => 'Passkey hernoemd.',
        'delete' => 'Verwijderen',
        'delete_confirmation' => 'Weet je zeker dat je de passkey ":name" wilt verwijderen?',
        'never_used' => 'Nooit gebruikt',
        'columns' => [
            'name' => 'Naam',
            'last_used' => 'Laatst gebruikt',
            'actions' => 'Acties',
        ],
        'registered' => 'Passkey succesvol geregistreerd.',
        'deleted' => 'Passkey verwijderd.',
    ],

    'challenge' => [
        'unsupported' => 'Je browser ondersteunt geen passkeys.',
        'failed' => 'De passkey kon niet geverifieerd worden. Probeer het opnieuw.',
        'callout' => [
            'heading' => 'Aanmelden met een passkey',
            'description' => 'Ga verder om een passkey te kiezen op je apparaat of in je wachtwoordmanager.',
            'waiting' => 'Wachten op je passkey…',
        ],
    ],

    'register' => [
        'name_label' => 'Naam van passkey',
    ],
];

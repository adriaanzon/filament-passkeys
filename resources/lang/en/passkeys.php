<?php

return [
    'provider' => [
        'label' => 'Use a passkey',
    ],

    'management' => [
        'heading' => 'Passkeys',
        'description' => 'Passkeys let you sign in using your fingerprint, face, or device PIN.',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'empty' => 'No passkeys registered yet.',
        'add' => 'Add a passkey',
        'waiting' => 'Waiting for device…',
        'default_name' => 'Passkey',
        'rename' => 'Rename',
        'renamed' => 'Passkey renamed.',
        'delete' => 'Delete',
        'delete_confirmation' => 'Are you sure you want to delete the passkey ":name"?',
        'never_used' => 'Never used',
        'columns' => [
            'name' => 'Name',
            'last_used' => 'Last used',
            'actions' => 'Actions',
        ],
        'registered' => 'Passkey registered successfully.',
        'deleted' => 'Passkey deleted.',
    ],

    'challenge' => [
        'unsupported' => 'Your browser doesn\'t support passkeys.',
        'failed' => 'That passkey couldn\'t be verified. Please try again.',
        'callout' => [
            'heading' => 'Sign in with a passkey',
            'description' => 'Continue to choose a passkey from your device or password manager.',
            'waiting' => 'Waiting for your passkey…',
        ],
    ],

    'register' => [
        'name_label' => 'Passkey name',
    ],
];

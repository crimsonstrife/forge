<?php

/**
 * Configuration for mentions.
 *
 * This configuration file defines the pools for mentions, which specify the models that can be mentioned,
 * the column used to search the model, the route used to generate the user link, and the notification class
 * to use when the model is mentioned.
 *
 * @return array
 */
return [
    'pools' => [
        'users' => [
            // Model that will be mentioned.
            'model' => App\Models\User::class,

            // The column that will be used to search the model by the parser.
            'column' => 'username',

            // The route used to generate the user link.
            'route' => '/users/profile/@',

            // Notification class to use when this model is mentioned.
            'notification' => App\Notifications\MentionNotification::class,
        ]
    ]
];

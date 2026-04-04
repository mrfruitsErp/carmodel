<?php

return [

    'platforms' => [

        'autoscout24' => [
            'name'                 => 'AutoScout24',
            'enabled'              => env('AUTOSCOUT24_ENABLED', false),
            'auth_type'            => 'oauth2',
            'supports'             => ['publish', 'update', 'delete', 'stats', 'leads'],
            'required_credentials' => ['client_id', 'client_secret'],
            'docs_url'             => 'https://listing-creation.api.autoscout24.com/docs',
        ],

        'automobile_it' => [
            'name'                 => 'Automobile.it',
            'enabled'              => env('AUTOMOBILE_IT_ENABLED', false),
            'auth_type'            => 'api_key',
            'supports'             => ['publish', 'update', 'delete', 'stats', 'leads', 'webhook'],
            'required_credentials' => ['api_key'],
            'docs_url'             => 'https://api.automobile.it/v2/docs',
        ],

        'ebay_motors' => [
            'name'                 => 'eBay Motors',
            'enabled'              => env('EBAY_MOTORS_ENABLED', false),
            'auth_type'            => 'oauth2_user',
            'supports'             => ['publish', 'update', 'delete', 'leads'],
            'required_credentials' => ['app_id', 'cert_id', 'refresh_token'],
            'optional_credentials' => ['policies'],
            'docs_url'             => 'https://developer.ebay.com/api-docs/sell/inventory/overview.html',
        ],

        'subito_it' => [
            'name'                 => 'Subito.it',
            'enabled'              => env('SUBITO_IT_ENABLED', false),
            'auth_type'            => 'browser_automation',
            'supports'             => ['publish', 'update', 'delete'],
            'required_credentials' => ['email', 'password'],
            'note'                 => 'Nessuna API ufficiale — usa automazione browser',
        ],

        'facebook_marketplace' => [
            'name'                 => 'Facebook Marketplace',
            'enabled'              => env('FACEBOOK_MARKETPLACE_ENABLED', false),
            'auth_type'            => 'page_token',
            'supports'             => ['publish', 'update', 'delete', 'stats'],
            'required_credentials' => ['page_access_token', 'catalog_id'],
            'optional_credentials' => ['page_id'],
            'docs_url'             => 'https://developers.facebook.com/docs/marketing-api/catalog/vehicles',
        ],

    ],

    'queue' => env('MARKETPLACE_QUEUE', 'marketplace'),

    'max_photos' => [
        'autoscout24'          => 20,
        'automobile_it'        => 30,
        'ebay_motors'          => 12,
        'subito_it'            => 16,
        'facebook_marketplace' => 10,
        'default'              => 15,
    ],

];
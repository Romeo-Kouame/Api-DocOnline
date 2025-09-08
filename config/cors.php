<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chemins autorisés pour CORS
    |--------------------------------------------------------------------------
    |
    | Définissez ici les chemins qui doivent accepter les requêtes cross-origin.
    | '*' autorise toutes les routes, mais vous pouvez limiter à '/api/*'.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Origines autorisées
    |--------------------------------------------------------------------------
    |
    | Définissez ici les URLs de vos front-ends autorisés.
    | Exemple : 'http://localhost:5173' pour Vite + React.
    | '*' autorise toutes les origines (pas recommandé en production).
    |
    */

   
    /*
    |--------------------------------------------------------------------------
    | Origines autorisées avec regex
    |--------------------------------------------------------------------------
    */

     'allowed_origins' => [
        'http://localhost:5173',
        // 'http://192.168.100.4:5173'
    ],

    /*
    |--------------------------------------------------------------------------
    | Méthodes HTTP autorisées
    |--------------------------------------------------------------------------
    */

    'allowed_methods' => ['*'], // GET, POST, PUT, PATCH, DELETE, OPTIONS

    /*
    |--------------------------------------------------------------------------
    | En-têtes autorisés
    |--------------------------------------------------------------------------
    */

    'allowed_headers' => ['*'], // Exemple : ['Content-Type', 'X-Requested-With']

    /*
    |--------------------------------------------------------------------------
    | En-têtes exposés
    |--------------------------------------------------------------------------
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Autoriser les cookies / credentials
    |--------------------------------------------------------------------------
    |
    | Important si tu utilises axios avec `withCredentials: true`.
    |
    */

    'supports_credentials' => true,

    /*
    |--------------------------------------------------------------------------
    | Temps de cache pour OPTIONS preflight
    |--------------------------------------------------------------------------
    */

    'max_age' => 0,

];

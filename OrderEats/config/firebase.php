<?php

return $app['config']['firebase'] = [
      'credentials' => [
          'file' => env('FIREBASE_CREDENTIALS'),
          'databaseUrl' => env('FIREBASE_DATABASE_URL'),
      ],
  ];

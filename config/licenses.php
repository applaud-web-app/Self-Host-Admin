<?php
return [
    // pulls from .env, fallback to a dummy for local/dev
    'secret' => env('LICENSE_SECRET', 'local-dev-secret'),
];

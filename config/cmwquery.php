<?php

return [
    'project_id' => env('CMW_PROJECT', 'e5c6bfa3-ef4a-b95c-fbb5-ee092896ea36'),  // main e5c6bfa3-ef4a-b95c-fbb5-ee092896ea36 // my

    'api_url' => env('CMW_API_URL', 'https://1pls1.comindwork.com/api/apialpha.ashx/tickets/multi'),

    'auth_code' => 'CMW_AUTH_CODE '.env('CMW_AUTH_CODE'),

    'domain' => 'oneplusone.solutions',
    'process_template_id' => 'default-app-crm-lead',
    'state' => 'lead-warm',

    'fields' => [
        'name' => 'name',
        'email' => 'email',
        'message' => 'body',
        'phone' => 'phone',
        'files' => 'files',
    ],

];

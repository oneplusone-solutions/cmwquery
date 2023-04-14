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
        'phone' => 'phone',
        'country' => 'country',
        'website' => 'website',

        'message' => 'body',
        'files' => 'files',

        'company' => 'company',
        'sector' => 'sector', // Can be: Consumer Discretionary, Consumer Staples, Energy, Financials, Health Care, Industrials, Information Technology, Materials, Real Estate, Telecommunication Services, Utilities
        'employer' => 'employer', //Size of company can be: size1-10, size11-50, size51-200, size201-500, size501-1000, size1001-5000, size5001-10000, size10000
        'position' => 'position', // your staff position
        'role' => 'role', //Work role may be: ceo, communications, consulting, customer_service, education, engineering, finance, founder, health_professional, human_resources, information_technology, legal, marketing, operations, owner, president, product, public_relations, real_estate, recruiting, research, sales
        'domain' => 'domain',

        'facebook' => 'facebook',
        'instagram' => 'instagram',
        'twitter' => 'twitter',
        'youtube' => 'youtube',
        'linkedin' => 'linkedin',
    ],

];

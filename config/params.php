<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: throw new RuntimeException('JWT_SECRET não configurado'),
        'issuer' => getenv('JWT_ISSUER') ?: 'atletis-api',
        'expire' => (int) (getenv('JWT_EXPIRE') ?: 3600),
    ],
];

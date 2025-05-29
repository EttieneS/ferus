<?php
    return [
        'paths' => ['api/*', 'auth/*'],
        'allowed_methods' => ['*'],
        'allowed_origins' => ['https://melio.dev.smith.com:4200'],
        'allowed_headers' => ['*'],
        'supports_credentials' => true,
    ];
?>

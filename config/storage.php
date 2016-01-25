<?php

return [
    'local' => [
        'type' => 'Local',
        'root' => dirname(__FILE__).'/../backups/database/',
    ],
    's3' => [
        'type' => 'AwsS3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'version' => 'latest',
        'bucket' => '',
        'root' => '',
    ]
];
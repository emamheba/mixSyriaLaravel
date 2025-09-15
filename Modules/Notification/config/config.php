<?php

return [
    'name' => 'Notification',
    
    'channels' => [
        'database' => true,
        'pusher' => true,
        'email' => false,
        'sms' => false,
    ],
    
    'default_types' => [
        [
            'name' => 'System Notification',
            'slug' => 'system',
            'description' => 'Platform updates and maintenance alerts',
            'default_channels' => ['database', 'pusher']
        ],
        [
            'name' => 'Message Notification',
            'slug' => 'message',
            'description' => 'New messages received',
            'default_channels' => ['database', 'pusher']
        ],
        // ... other types
    ]
];
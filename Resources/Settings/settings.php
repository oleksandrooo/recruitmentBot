<?php

return [
    'key' => '816683941:AAGCxvaxbGcXM99VTwsuk6S5AhyheZLNQNY',
    'ignoreSession' => [],
    'dbPrefix' => 'bot_',
    'telegramDefaultParams' => [
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
        'disable_notification' => true
    ],
    'telegramListOfMethods' => [
        'sendMessage' => ['parse_mode', 'disable_web_page_preview', 'disable_notification'],
        'forwardMessage' => ['disable_notification']
    ]
];

<?php

return [
    'key' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',
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

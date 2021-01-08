<?php

namespace Core;

class Telegram
{
    private static string $allUrl;
    private static array $defaultParams;
    private static array $telegramListOfMethods;

    public static array $data;
    private static array $from;
    private static array $chat = [];

    public static function start(array $keys): void
    {
        /*отримуємо параметри з налаштувань ('key', 'telegramDefaultParams', 'telegramListOfMethods')
         * key - ключ до бота,
         * telegramDefaultParams - масив значень за замовчуванням  Resources/Settings/settings.php
         * telegramListOfMethods - які значення за замовчуванням до яких методів застосовуються
         */
        self::$defaultParams = $keys['telegramDefaultParams'];
        self::$telegramListOfMethods = $keys['telegramListOfMethods'];
        //робимо стрічку типу https://api.telegram.org/bot123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
        self::$allUrl = 'https://api.telegram.org/bot' . $keys['key'] . '/';
        //якщо дебажимо, то робимо через getUpdates, а якщо на сервері, то через Webhook
        //https://core.telegram.org/bots/api#getting-updates
        if (REGIME === 'debug') {
            $data = json_decode(file_get_contents(self::$allUrl . 'getUpdates'), JSON_OBJECT_AS_ARRAY);
            $count = count($data['result']) - 1;
            //в $data - результат
            self::$data = $data['result'][$count];
            echo '<details><summary>Отримуємо запит</summary>';
            print_r($data['result'][$count]);
            echo '</details>';
        } elseif (REGIME === 'server') {
            $data = json_decode(file_get_contents('php://input'), JSON_OBJECT_AS_ARRAY);
            self::$data = $data;
        }
        //дивимось який запит, і зберігаємо у $from від кого запит, а в $chat - інфа про чат, якщо вона є
        //https://core.telegram.org/bots/api#update
        $allKeys = array_keys(self::$data);
        switch ($allKeys[1]) {
            case 'message':
                self::$from = self::$data['message']['from'];
                self::$chat = self::$data['message']['chat'];
                define('TELEGRAM_TEXT', self::$data['message']['text']);
                break;
            case 'edited_message':
                self::$from = self::$data['edited_message']['from'];
                if (isset(self::$data['edited_message']['chat'])) {
                    self::$chat = self::$data['edited_message']['chat'];
                }
                define('TELEGRAM_TEXT', self::$data['edited_message']['text']);
                break;
            case 'callback_query':
                self::$from = self::$data['callback_query']['from'];
                if (isset(self::$data['callback_query']['message'])) {
                    self::$chat = self::$data['callback_query']['message']['chat'];
                }
                break;
            default:
                echo 'error';
        }
        //також дуже часто використовується id користувача і код мови (в UserInterface особливо), тому зберігаємо
        // в константу
        define('USER_ID', self::$from['id']);
        define('LANGUAGE_CODE', self::$from['language_code']);
    }

    public static function getFromObject(): array
    {
        return self::$from;
    }

    public static function getChatObject(): ?array
    {
        if (!empty(self::$chat)) {
            return self::$chat;
        }

        return null;
    }

    public static function getDataObject(): array
    {
        return self::$data;
    }

    /**
     * @param string $method - назва методу, наприклад sendMessage
     * @param array $params - параметри, типу ['chat_id' => 1111, 'text' => 'hello']
     */
    public static function sendRequest(string $method, $params = []): void
    {
        $defaultParams = [];
        //якщо для цього методу в налаштуваннях задані стандартні параметри, то додаємо їх в масив
        if (!empty(self::$telegramListOfMethods[$method])) {
            foreach (self::$telegramListOfMethods[$method] as $key) {
                $defaultParams[$key] = self::$defaultParams[$key];
            }
        }
        //якщо масив з вхідними параметрами не пустий, то об'єднуємо його з стандартними параметрами
        if (!empty($params)) {
            $reqParams = array_merge($defaultParams, $params);
        } else {
            $reqParams = $defaultParams;
        }
        //відправляємо запит
        $query = self::$allUrl . $method . '?' . http_build_query($reqParams);
        if (REGIME === 'debug') {
            echo '<details><summary>Вихідний запит до телеграма</summary>';
            echo $query . '</details>';
            $res = file_get_contents($query);
            echo '<details><summary>Відповідь на запит від телеграма</summary>';
            echo $res . '</details>';
        } else {
            file_get_contents($query);
        }
    }
}

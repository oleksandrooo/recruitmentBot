<?php

namespace Core;

class Markups
{
    //цей метод формує Reply клавіатуру. В $params передаються значення, які необов'язкові - resize_keyboard,
    // one_time_keyboard і selective
    //https://core.telegram.org/bots/api#replykeyboardmarkup
    public static function makeReplyKeyboard(string $name, array $params = []): string
    {
        //підключаємо файл з необхідною клавіатурою. Надіюсь що вона є, тому перевірок немає, та і так якщо що
        // нічого працювати не буде, тому без перевірки
        $info = require $_SERVER['DOCUMENT_ROOT'] . "/Resources/Markups/replyMarkups/" . $name . '.php';
        //перебираємо кожну кнопку, і заміняємо її значення на значення з файлу локалізації
        array_walk_recursive($info['buttons'], static function (&$item) {
            $item = UserInterface::getCommand($item);
        });
        $result = ['keyboard' => $info['buttons']];
        //якщо є параметри, то перебираємо і їх додаємо
        if (!empty($params)) {
            $somePossibleParams = ['resize_keyboard', 'one_time_keyboard', 'selective'];
            foreach ($somePossibleParams as $possibleParam) {
                if (isset($params[$possibleParam]) && is_bool($params[$possibleParam])) {
                    $result += [$possibleParam => $params[$possibleParam]];
                }
            }
        }
        //вертаємо готову клавіатуру
        return json_encode($result);
    }

    //Цей метод займається inline клавіатурами. Зазвичай в inline кнопках є data типу name__1, для ідентифікації, і
    //тому в $params передаються значення які підставити у значення ідентифікатора. Наприклад,
    //['id' = 2]
    public static function makeInlineKeyboard(string $name, array $params = []): string
    {
        //отримуємо inline клавіатуру
        $info = require $_SERVER['DOCUMENT_ROOT'] . "/Resources/Markups/inlineMarkups/" . $name . '.php';
        $buttons = [];
        foreach ($info['buttons'] as $lineNumber => $buttonsLine) {
            foreach ($buttonsLine as $buttonNumber => $button) {
                //перебираємо кожну кнопку, і заміняємо її значення на значення з файлу локалізації
                $buttons[$lineNumber][$buttonNumber]['text'] = UserInterface::getCommand($button[0]);
                //якщо клавіатура змінюванна, то підставляємо в callback_data значення, які треба для ідентифікації
                if ($info['editable'] === true) {
                    $buttons[$lineNumber][$buttonNumber]['callback_data'] = preg_replace_callback(
                        '#%(.*)%#isU',
                        function (array $matches) use ($params) {
                            return $params[$matches[1]];
                        },
                        $button[1]
                    );
                    //а якщо вона незмінювана, то просто зберігаємо в callback_data значення які задані
                } else {
                    $buttons[$lineNumber][$buttonNumber]['callback_data'] = $button[1];
                }
            }
        }
        return json_encode(array("inline_keyboard" => $buttons));
    }
}
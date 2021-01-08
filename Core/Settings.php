<?php

namespace Core;

class Settings
{
    private static array $settingsList;

    public static function getValue($name)
    {
        //якщо немає масиву з налаштуваннями то підключаємо
        if (empty(self::$settingsList)) {
            $path = $_SERVER['DOCUMENT_ROOT'] . "/Resources/Settings/";
            //підключаємо файл з налаштуваннями, які не залежать від режиму роботи
            $mainArr = require $path . 'settings.php';
            //підключаємо файл з налаштуванням в залежності від режиму роботи
            $addArr = require $path . 'setting ' . REGIME . '.php';
            //об'єднуємо їх в один масив
            self::$settingsList = array_merge($mainArr, $addArr);
        }
        //перевіряємо чи це масив параметрів, які треба
        if (is_array($name) && !empty($name)) {
            $result = [];
            //якщо масив, то перебираємо кожне значення і формуємо асоціативний масив типу параметр=>значення
            foreach ($name as $key) {
                $result[$key] = self::$settingsList[$key];
            }
            return $result;
        }
        //а якщо то стрічка, тобто одне ім'я, то вертаємо відповідне значення параметру
        return self::$settingsList[$name] ?? null;
    }

    //функція для додавання параметру і його значення
    public static function addValue(string $name, $value): void
    {
        self::$settingsList[$name] = $value;
    }
}

<?php

namespace Core;

class UserInterface
{
    private static array $dateFormats;
    private static array $allCommands;
    private static array $phrases;
    private static array $importantCommands;

    private static function includeFile(string $name): void
    {
        /*
         * деякі фрази чи кнопки можуть мати однакове ім'я для всіх мов, то щоб не повторювати вони зберігаються в
         * Resources/Localizations/default
         */
        $defaultLocalization = require $_SERVER['DOCUMENT_ROOT'] . "/Resources/Localizations/" .
            'default' . '/' . $name . '.php';
        //якщо існує локалізація для цієї мови, то підключаємо відповідний файл який треба (чи з назвами команд,
        //кнопок, чи ще чимось), і об'єднуємо з файлом, який в папці Resources/Localizations/default
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . "/Resources/Localizations/" . LANGUAGE_CODE)) {
            $localization = require $_SERVER['DOCUMENT_ROOT'] . "/Resources/Localizations/" .
                LANGUAGE_CODE . '/' . $name . '.php';
            self::${$name} = array_merge($defaultLocalization, $localization);
        } else {
            self::${$name} = $defaultLocalization;
        }
    }

    //отримуємо команду типу ['start' => 'Старт']. Якщо ім'я команди не задано, то повертаємо масив з командами
    public static function getCommand(string $nameCommand = '')
    {
        if (empty(self::$allCommands)) {
            self::includeFile('allCommands');
        }
        if (empty(self::$importantCommands)) {
            self::includeFile('importantCommands');
        }
        self::$allCommands = array_merge(self::$allCommands, self::$importantCommands);
        if (empty($nameCommand)) {
            return self::$allCommands;
        }

        return self::$allCommands[$nameCommand];
    }

    //отримуємо обернену команду, типу ['Старт' => 'start']. Цей метод треба для index.php, де таким чином визначається
    //який контролер треба і шукається відповідний шлях
    public static function getFlipCommand(string $nameCommand = '')
    {
        if (empty(self::$allCommands)) {
            self::includeFile('allCommands');
        }
        if (empty(self::$importantCommands)) {
            self::includeFile('importantCommands');
        }
        self::$allCommands = array_merge(self::$allCommands, self::$importantCommands);
        if (empty($nameCommand)) {
            return array_flip(self::$allCommands);
        }

        $tmp = array_flip(self::$allCommands);
        return $tmp[$nameCommand];
    }

    //отримуємо формат дати
    public static function getDateFormat(string $name): string
    {
        if (empty(self::$dateFormats)) {
            self::includeFile('dateFormats');
        }
        return self::$dateFormats[$name];
    }

    //отримуємо якусь фразу
    public static function getPhrase(string $phrase): string
    {
        if (empty(self::$phrases)) {
            self::includeFile('phrases');
        }
        return self::$phrases[$phrase];
    }

    //отримуємо важливу команду (треба для index.php)
    public static function getImportantCommands(string $nameCommand = ''): array
    {
        if (empty(self::$importantCommands)) {
            self::includeFile('importantCommands');
        }
        if (empty($nameCommand)) {
            return self::$importantCommands;
        }
        return self::$importantCommands[$nameCommand];
    }
}

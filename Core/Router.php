<?php

namespace Core;

class Router
{
    private static string $partOfPath;
    private static array $paths;

    //ця функція отримує шлях до файлу контролера
    public static function getPath(string $commandName): string
    {
        if (empty(self::$partOfPath)) {
            self::$partOfPath = $_SERVER['DOCUMENT_ROOT'] . '/Controllers/';
        }
        //отримуємо файл з шляхами
        if (empty(self::$paths)) {
            self::$paths = require $_SERVER['DOCUMENT_ROOT'] . '/Resources/paths.php';
        }
        //якщо команда є то отримуємо шлях до файлу, а якщо немає то просто пусту стрічку вертаємо
        if (!empty(self::$paths[$commandName])) {
            return self::$partOfPath . self::$paths[$commandName] . '.php';
        }
        return '';
    }
}

<?php

namespace Core;

use PDO;

class CurrentStateMachine
{
    private static string $tableName = 'states';
    private static array $allStates;
    private static array $notIgnoreStates;
    private static int $userId;
    private static int $stateLife;


    /**
     * @param string $name - ім'я стейта
     * @param bool $importantValue - чи важливий і не ігнорити, чи просто там якісь текстові значення і не дуже важливий
     * @param string $value - значення. Інколи треба просто важливий стейт, і значення і не треба, тому можна залишити
     * по дефолту
     * Повинно було б виводити чи успішно додалось, чи ні, але я такий собі погроміст, тому нічого не виводить, і так
     * дізнаюсь
     */
    public static function addState(string $name, bool $importantValue = false, $value = 'value'): void
    {
        $date = time();
        DataBase::insertIntoTable(self::$tableName, [
            'id' => null,
            'date' => $date,
            'name' => $name,
            'value' => $value,
            'user_id' => self::$userId,
            'ignoreState' => $importantValue
        ]);
    }
    //Отримуємо все про стейт за його ім'ям (id, дату коли створений, хто створив, значення, і чи важлиивий)
    public static function getState(string $name): array
    {
        return DataBase::getFromTable(
            self::$tableName,
            ["where" => sprintf("`name`='{%s}' AND `user_id`={%d}", $name, self::$userId),
                'fetchTypePDO' => PDO::FETCH_ASSOC]
        );
    }
    //Видаляємо стейт за його назвою і для цього користувача, який цю функцію викликав. Бо може бути однакові стейти
    //для різних користувачів, то щоб чужий стейт не затерся
    public static function delState($name): void
    {
        DataBase::deleteFromTable(
            self::$tableName,
            sprintf("`name`='{%s}' AND `user_id`={%d}", $name, self::$userId)
        );
    }
    /*
     * Найперша функція яка викликається. В ній отримуємо всі стейти для цього користувача, і перебираємо.
     * Важливі стейти ми зберігаємо в масив $notIgnoreStates, і також взагалі всі стейти додаємо в $allStates
     */
    public static function getAllStates(int $id): array
    {

        self::$userId = $id;
        $result = DataBase::getFromTable(
            self::$tableName,
            ["name" => [self::$tableName => ['name', 'value', 'ignoreState']],
                'where' => "`user_id` = {$id}", 'fetchTypePDO' => PDO::FETCH_ASSOC]
        );

        self::$notIgnoreStates = [];
        foreach ($result as $value) {
            if ($value['ignoreState'] == 0) {
                self::$notIgnoreStates[$value['name']] = $value['value'];
            }
            self::$allStates[$value['name']] = $value['value'];
        }
        return self::$allStates;
    }

    //видаляємо всі стейти для користувача (використовується для команди /cancel
    public static function delAllStates($id): void
    {
        DataBase::deleteFromTable(self::$tableName, "`user_id` = {$id}");
    }

    //Щоб не засмічувалась БД видаляємо старі стейти, термін життя задається в налаштуваннях і залежить від режиму
    public static function destruct()
    {
        $time = time() - self::$stateLife;
        DataBase::deleteFromTable(self::$tableName, "`date` < {$time}");
    }

    //отримуємо важливі стейти
    public static function getNotIgnoreStates(): array
    {
        return self::$notIgnoreStates;
    }
}

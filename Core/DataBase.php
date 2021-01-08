<?php

namespace Core;

use PDO;
use PDOStatement;

class DataBase
{
    private static PDO $con;
    private static string $dbPrefix;

    //Найперша функція, яка виконується. Тут відбувається підключення до БД. Розширення PDO
    public static function start(array $params): void
    {
        //отримуємо значення префіксу для таблиці і БД (для захисту), і зберігаємо його в статичній змінній
        self::$dbPrefix = $params['dbPrefix'];
        $dsn = sprintf('mysql:host=%s;dbname=%s', $params['dbHost'], $params['dbName']);
        //виконуємо підключення
        self::$con = new PDO(
            $dsn,
            $params['dbUser'],
            $params['dbPassword'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        //встановлюємо кодування
        self::$con->exec('SET NAMES UTF8');
    }

    /**
     * @param string $table - назва таблиці
     * @param array $params - асоціативний масив виду ['назваПоля' => 'значенняПоля']
     */
    public static function insertIntoTable(string $table, array $params = []): void
    {
        //зберігаємо в масив значення полів
        $keys = array_keys($params);
        //робимо запит виду "INSERT INTO `prefix_table` (`field1`, `field2`...) VALUES (:value1, :value2...)"
        $stm = self::$con->prepare(sprintf(
            'INSERT INTO `%s%s` (`%s`) VALUES (:%s)',
            self::$dbPrefix,
            $table,
            implode('`, `', $keys),
            implode(', :', $keys)
        ));
        //за замовчуванням PDO всі значення передає як стрічки, тому щоб такого не було перебираємо всі значення
        //і робимо так щоб вони передавались з правильним типом
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $stm->bindValue(':' . $key, $value, PDO::PARAM_BOOL);
            } elseif (is_numeric($value)) {
                $stm->bindValue(':' . $key, $value, PDO::PARAM_INT);
            } elseif (is_null($value)) {
                $stm->bindValue(':' . $key, $value, PDO::PARAM_NULL);
            } else {
                $stm->bindValue(':' . $key, $value);
            }
        }
        //додаємо в таблицю
        $stm->execute();
        if (REGIME === 'debug') {
            echo '<details><summary>Вставляємо щось і отримуємо результат</summary>';
            $stm->debugDumpParams();
            echo '</details>';
        }
    }

    /**
     * @param string $table - назва таблиці
     * @param array $params - асоціативний масив який може мати ключі:
     * ['name' => NULL:array - масив типу ['table1' => ['column1','column2']]. Таким чином пишемо з якої таблиці які
     * поля треба вибрати. Якщо всі поля, то цей ключ не потрібен
     * 'join' => NULL:array[[ //виконуємо join
     *      'joinType' => string - INNER, LEFT, RIGHT ....
     *      'tableName' => string - таблиця яка підключається
     *      'condition' => string - умова (передаємо стрічку, яка вставляється як умова)
     * ], ... ]
     * 'where' => NULL:string - умова для вибірки. Так як умову наперед передбачити майже неможливо, то тут передається
     * стрічка, яка вставляється як умова
     * 'sort' => NULL:string - поле за яким сортуємо
     * 'sortDESC' => NULL:true - якщо сортувати за спаданням то true, якщо ні то не передаємо
     * 'limit' => NULL:int - ліміт
     * 'offset' => NULL:int - зміщення
     * 'fetchTypePDO' => PDO::FETCH_ASSOC|PDO::FETCH_NAMED... https://www.php.net/manual/ru/pdo.constants.php
     * @return array
     */
    public static function getFromTable(string $table, array $params = [])
    {

        $query = '';
        //Якщо є 'name' то перебираємо масив і вставляємо які поля з якої таблиці треба
        if (isset($params['name']) && is_array($params['name'])) {
            $keysOfQuery = '';
            foreach ($params['name'] as $key => $value) {
                foreach ($value as $iValue) {
                    $keysOfQuery .= sprintf(' `%s%s`.`%s`,', self::$dbPrefix, $key, $iValue);
                }
            }
            $keysOfQuery = mb_substr($keysOfQuery, 0, -1);
            $query .= sprintf('SELECT %s FROM `%s%s`', $keysOfQuery, self::$dbPrefix, $table);
        //Якщо немає 'name', то вставляємо *
        } else {
            $query .= sprintf('SELECT * FROM `%s%s`', self::$dbPrefix, $table);
        }
        //если есть join, то підключаємо
        if (isset($params['join']) && is_array($params['join'])) {
            foreach ($params['join'] as $iValue) {
                $query .= sprintf(
                    ' %s JOIN `%s%s` %s',
                    $iValue['joinType'],
                    self::$dbPrefix,
                    $iValue['tableName'],
                    $iValue['condition']
                );
            }
        }
        //Стрічка з умовами для вибірки
        if (isset($params['where']) && is_string($params['where'])) {
            $query .= sprintf(' WHERE %s', $params['where']);
        }
        //Стрічка для сортування за спаданням чи спаданням
        if (isset($params['sort']) && is_string($params['sort'])) {
            if (isset($params['sortDESC']) && $params['sortDESC'] === 'DESC') {
                $query .= sprintf(' ORDER BY `%s` DESC', $params['sort']);
            } else {
                $query .= sprintf(' ORDER BY `%s`', $params['sort']);
            }
        }
        //вставляємо ліміт
        if (isset($params['limit']) && is_numeric($params['limit'])) {
            $query .= sprintf(' LIMIT %d', $params['limit']);
        }
        //вставляємо offset
        if (isset($params['offset']) && is_numeric($params['offset'])) {
            $query .= sprintf(' OFFSET %d', $params['offset']);
        }
        //виконуємо запит
        $result = self::execRequest($query);
        //якщо є ключ як сортувати результати, то використовуємо його, а якщо немає то немає
        if (isset($params['fetchTypePDO'])) {
            $finalResult = $result->fetchAll($params['fetchTypePDO']);
        } elseif ($result === false) {
            $finalResult = false;
        } else {
            $finalResult = $result->fetchAll();
        }
        return $finalResult;
    }

    /**
     * @param string $table
     * @param string $cond - умова за якою видалити записи
     * @return bool
     */
    public static function deleteFromTable(string $table, string $cond = ''): bool
    {
        $query = sprintf('DELETE FROM `%s%s`', self::$dbPrefix, $table);
        if (!empty($cond)) {
            $query .= sprintf(' WHERE %s', $cond);
        }
        return self::execRequest($query);
    }

    /* @param string $query - тут виконуємо всі запити, які неможливо підготувати через prepare
     *
     * @return bool|false|PDOStatement
     */
    public static function execRequest(string $query)
    {
        if (REGIME === 'debug') {
            echo '<details><summary>Виконуємо якийсь запит до БД</summary>';
            echo $query . '</details>';
            //print_r($query);
        }
        return self::$con->query($query);
    }

    /**
     * @param string $table
     * @param array $params -як і у випадку з insertIntoTable, асоціативний масив зі значеннями, які треба оновити
     * @param string $cond - умова
     * @return bool|false|PDOStatement
     */
    public static function updateInTable(string $table, array $params, string $cond)
    {
        $query = sprintf('UPDATE `%s%s` SET', self::$dbPrefix, $table) ;
        $tmp = '';
        //формуємо стрічку з полями і значеннями, які треба замінити
        foreach ($params as $key => $value) {
            if (is_string($value) && substr_count($value, '`') === 0) {
                $tmp .= sprintf(" `%s` = '%s',", $key, $value);
            } else {
                $tmp .= sprintf(" `%s` = %s,", $key, $value);
            }
        }
        $query .= mb_substr($tmp, 0, -1);
        //додаємо умову
        $query .= sprintf(" WHERE %s", $cond);
        return self::execRequest($query);
    }
    //отримуємо кількість записів у таблиці
    public static function getCount(string $table, string $cond = '')
    {
        $query = sprintf('SELECT COUNT(*) FROM `%s%s`', self::$dbPrefix, $table);
        if (!empty($cond)) {
            $query .= sprintf(' WHERE %s', $cond);
        }
        $result = self::execRequest($query);
        $finalResult = $result->fetchAll(PDO::FETCH_COLUMN);
        return $finalResult[0];
    }
}

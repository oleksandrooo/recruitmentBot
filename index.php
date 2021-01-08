<?php

declare(strict_types=1);

use Core\CurrentStateMachine;
use Core\DataBase;
use Core\Router;
use Core\Settings;
use Core\Telegram;
use Core\UserInterface;

//підключаємо класи, в regime прописаний режим - дебаг чи продакшн
require_once 'regime.php';
spl_autoload_register(static function (string $className) {
    require_once sprintf("%s/%s.php", __DIR__, str_replace("\\", "/", $className));
});
//отримуємо запит
Telegram::start(Settings::getValue(['key', 'telegramDefaultParams', 'telegramListOfMethods']));
//підключаємось до БД
DataBase::start(Settings::getValue(['dbHost', 'dbUser', 'dbPassword', 'dbName', 'dbPrefix']));

//дивимся які є карент стейти, і які ігнорити, а які - ні
$allStates = CurrentStateMachine::getAllStates(USER_ID);
$notIgnoreStates = CurrentStateMachine::getNotIgnoreStates();

/*
 * розбираємось з тим, який треба контролер підключати. Для цього аналізуємо запит і задаємо перевірки
 * Зі свого досвіду ось такий порядок перевірки:
 * 1. Важлива команда, щоб можна було все скасувати, якщо щось пішло не так
 * 2. Наявні карент стейти, щоб адекватно працював
 * 3. Калбеки
 * 4. Прості текстові команди
 */


//Дивимось чи це пріоритетна команда, типу /cancel
//дивимось чи взагалі є текст (в callback немає)
if (isset(Telegram::getDataObject()['message']['text'])) {
    //отримуємо список важливих команд
    $listOfImportantCommands = UserInterface::getImportantCommands();
    if (in_array(TELEGRAM_TEXT, $listOfImportantCommands, true)) {
        //якщо ця команда в списку важливих, то підключаємо контролер
        $commandName = UserInterface::getFlipCommand(TELEGRAM_TEXT);
        require Router::getPath($commandName);
        executionTime(__FILE__, __LINE__);
        exit();
    }
    //якщо не в тому списку, то дивимось чи це не важлива команда з параметром, наприклад /project_222.
    //Для цього перебираємо всі команди і дивимось чи є підстрічка (команда) в стрічці (отриманий текст)
    foreach ($listOfImportantCommands as $command) {
        if (substr_count(TELEGRAM_TEXT, $command) !== 0) {
            //якщо знайдена підстрічка в стрічці то підключаємо контролер
            $values = explode('__', TELEGRAM_TEXT);
            require Router::getPath($command);
            executionTime(__FILE__, __LINE__);
            exit();
        }
    }
}
//якщо то були не важливі команди, то дивимось карент стейти, і підключаємо необхідний контролер
//тут хоч і foreach, але зазвичай тільки один карент стейт, і бажано робити щоб був один
//Карент стейти зберігаються у вигляді асоціативного масиву, де назваСтейту => значення (або задане, або дефолтне)
if (!empty($notIgnoreStates)) {
    foreach ($notIgnoreStates as $state => $value) {
        $str = Router::getPath($state);
        if (!empty($str)) {
            require_once $str;
            exit();
        }
    }
}
//тепер дивимось чи цей запит - калбек. Data зазвичай має вигляд text__1, тому розбиваємо і маємо що
//$values[0] - назва команди, $values[1] - цифрове значення. Це треба для ідентифікації кнопки, щоб знати
//яку дію виконати і над чим
if (isset(Telegram::getDataObject()['callback_query'])) {
    $values = explode('__', Telegram::getDataObject()['callback_query']['data']);
    $str = Router::getPath($values[0]);
    if (!empty($str)) {
        require_once $str;
        exit();
    }
}
/*Якщо попередні перевірки всі не пройдені, то переходимо до останньої - дивимось чи це текстова команда, типу /start
 * Для цього робимо за таким же алгоритмом, як і з важливими командами, але тут вже всі команди беремо
 */
if (isset(Telegram::getDataObject()['message']['text'])) {
    $listOfAllCommands = UserInterface::getCommand();
    if (in_array(TELEGRAM_TEXT, $listOfAllCommands, true)) {
        $commandName = UserInterface::getFlipCommand(TELEGRAM_TEXT);
        require Router::getPath($commandName);
        executionTime(__FILE__, __LINE__);
        exit();
    }
    if (substr_count(TELEGRAM_TEXT, '_') !== 0) {
        $values = explode('_', TELEGRAM_TEXT);
        $str = Router::getPath(trim($values[0], '/'));
        if (!empty($str)) {
            require_once $str;
            executionTime(__FILE__, __LINE__);
            exit();
        }
    }
}

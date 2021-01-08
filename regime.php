<?php

const REGIME = 'debug';
if (REGIME === 'debug') {
    $starTime = microtime(true);
    $fd = fopen("time.txt", 'a');
    $lastTime = microtime(true);
    $fault = 0;
    function executionTime($file, $line)
    {
        global $fd, $lastTime, $starTime, $fault;
        static $count = 0;
        if ($count === 0) {
            fwrite($fd, '[' . date('G:i:s Y/m/d') . ']' . PHP_EOL);
        }
        fwrite($fd, 'Час виконання фрагменту: ' . round((microtime(true) - $lastTime - $fault), 7)
            . PHP_EOL . 'Час виконання скрипту: ' . round((microtime(true) - $starTime - $fault), 7)
            . PHP_EOL . 'Місце: ' . $file . ':' . $line . PHP_EOL);
        $count++;
        $lastTime = microtime(true);
        if ($count === 1) {
            $fault = microtime(true) - $starTime;
        }
    }
}
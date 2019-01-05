<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 05/01/2019
 * Time: 11:16 AM
 */

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/' . $path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}
spl_autoload_register('classLoader');
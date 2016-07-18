<?php

error_reporting(E_ALL);

$composer = dirname(__DIR__) . "/vendor/autoload.php";

if (!file_exists($composer)) {
    throw new Exception("It seems composer has not been installed as it's autoloader cannot be found, please run composer install");
}

require_once $composer;

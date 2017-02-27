<?php

session_start();

require(__DIR__ . '/config/config.php');

require_once(__DIR__ . '/config/Autoload.php');
Autoload::charger();

require_once(__DIR__ . '/config/SplClassLoader.php');


$myLibLoader = new SplClassLoader('controller', './');
$myLibLoader->register();
$myLibLoader = new SplClassLoader('config', './');
$myLibLoader->register();
$myLibLoader = new SplClassLoader('DAL', './');
$myLibLoader->register();
$myLibLoader = new SplClassLoader('modeles', './');
$myLibLoader->register();

new \controller\FrontController(__DIR__);

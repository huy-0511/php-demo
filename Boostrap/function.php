<?php
$autoloadController = function ($className) {
//    echo $className;
    $className = ucfirst($className);
    $fileName = 'Controller/' . $className . '.php';
    if (file_exists($fileName)) {
        require_once $fileName;
    }
};
spl_autoload_register($autoloadController);

$autoloadModel = function ($className) {
    $className = ucfirst($className);
    $fileName = 'Models/' . $className . '.php';
    if (file_exists($fileName)) {
        require_once $fileName;
    }
};

spl_autoload_register($autoloadModel);
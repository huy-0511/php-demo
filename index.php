<?php
require_once 'Boostrap/function.php';
require_once './vendor/autoload.php';
require_once 'Controller/ProductController.php';
require_once 'Controller/UserController.php';

$controller = isset($_GET['c']) ? ucfirst($_GET['c']). 'Controller' : 'UserController';
//echo $controller;
$action = isset($_GET['a']) ? $_GET['a'] : 'index';
//echo $action;
$param = isset($_GET['id']) ? $_GET['id'] : '';
//die();
//$class = 'api-mvc/App/Controller/'.$controller;
$controllers  = sprintf('\App\Controller\%s', $controller);
//echo $controllers;
if (class_exists($controllers))
{
    $object = new $controllers();
//    print_r($object);
    if (method_exists($object,$action))
    {
        if ($param) {
            $data = $object->$action($param);
        }else {
            $data = $object->$action();
        }
    }else {
        echo "aaa";
        require_once 'View/404.php';
    }
}else{
    echo "bbb";
    require_once 'View/404.php';
}

//use App\Models\Category;
//new Category();
//require_once 'Controller/CategoryController.php';
//$controller = new \App\Controller\CategoryController();
//
//var_dump(class_exists('\App\Controller\CategoryController

<?php

require_once 'Core/Config.php';
//require_once 'Core/Database.php';
require_once 'Core/CoreModel.php';
//require_once 'Controller/ProductController.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
//$id = isset($_GET['id']) ? $_GET['id'] : die();
$id = $_GET['id'];
$controller = new ProductController($id,$requestMethod);
$controller->readOne();
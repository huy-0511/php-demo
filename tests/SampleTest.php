<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use App\Models\Product;
require_once ('Models\Product.php');
class SampleTest extends TestCase
{
    public $model;
    public $client;
    public function testTrueIsTrue()
    {
        $foo = true;
        $this->assertTrue($foo);
    }
    public function testProduct()
    {
        $this->model = new Product();
        $data = $this->model->readPaginate(1,2);
//        $this->assertEquals($data);
        $this->assertEquals(200, $data->getStatusCode());
    }

}
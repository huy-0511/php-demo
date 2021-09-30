<?php
namespace App\Controller;
require_once 'Core/Config.php';
require_once ('Models/Product.php');
use App\Models\Product;

class ProductController
{
    private $modelProduct;
    public function __construct()
    {
        $this->modelProduct  = new Product();
    }
    public function getProduct()
    {
        if($_SERVER['REQUEST_METHOD'] == "GET"){
            $result = $this->modelProduct->get();
            $data_product = array();

            while ($data = $result->fetch_assoc()) {
                $data_product[] = $data;
            }
            echo json_encode(array(
                'data' => $data_product,
                'status' => 1,
                'message' => "Success"
            ));
        }
    }
    public function readOne()
    {
        if($_SERVER['REQUEST_METHOD'] == "GET") {
            if(isset($_GET['id'])) {
                $id = $_GET['id'];
                if (is_numeric($id) == true) {
                    $result = $this->modelProduct->find($id);
                    if($result){
                        echo json_encode(array(
                            'status' => 1,
                            "data"=> $result
                        ));
                    }
                }else{
                    echo json_encode(array(
                        'status' => 0,
                        "messqge"=> 'not integer'
                    ));
                }
            }
        }
    }
    public function store()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON);
            $name = $_POST['name'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $des = $_POST['description'];
//             print_r($_FILES);
//             die();
            $fileName = $_FILES['sendimage']['name'];
            $temPath = $_FILES['sendimage']['tmp_name'];
            $fileSize = $_FILES['sendimage']['size'];
//                $image = $fileName;
            if (empty($fileName)){
                http_response_code(404);
                $errorMsg = json_encode(array(
                    'status' => false,
                    'message' => "please select image"
                ));
                echo $errorMsg;
            }else{
                $upload_path = "upload/";

                $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
                $valid_extensions = array('jpeg','jpg','png','gif','webp');

                if (in_array($fileExt,$valid_extensions)){
                    if(!file_exists($upload_path . $fileName)){
                        if ($fileSize < 10000000){
                            move_uploaded_file($temPath, $upload_path . $fileName);
                        }else{
                            http_response_code(404);
                            $errorMsg = json_encode(array(
                                'status' => false,
                                'message' => "Sorry, your file is too large, please upload 5 MB size"
                            ));
                            echo $errorMsg;
                        }
                    }else{
                        http_response_code(404);
                        $errorMsg = json_encode(array(
                            'status' => false,
                            'message' => "Sorry, file already exists check upload folder"
                        ));
                        echo $errorMsg;
                    }
                }else{
                    http_response_code(404);
                    $errorMsg = json_encode(array(
                        'status' => false,
                        'message' => "Sorry, only JPG, JPEG, PNG, WEBP & GIF files are allowed"
                    ));
                    echo $errorMsg;
                }
            }
            if(!isset($errorMsg)){
                $data = [$name, $price, $quantity, $fileName, $des];
//                $data = [$names, $prices, $quantitys, $fileName, $description];
                $data_insert = $this->modelProduct->insert($data);
                echo json_encode(array(
                    'status' => true,
                    'message' => "Created Success"
                ));
            }
        }else{
            http_response_code(400);
            echo json_encode(array(
                'message' => "Error HTTP"
            ));
        }
    }
    public function update()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $inputJSON = file_get_contents('php://input');

//            if (isset($data) && !empty(($data))) {
            $input = json_decode($inputJSON, TRUE);
//                $request = json_decode($data);
            $id = $_POST['id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $des = $_POST['description'];
//            $id = $inputJSON->id;
//            $name = $inputJSON->name;
//            $price = $inputJSON->price;
//            $des = $inputJSON->description;
//             print_r($_FILES);
//             die();
            $fileName = $_FILES['sendimage']['name'];
            $temPath = $_FILES['sendimage']['tmp_name'];
            $fileSize = $_FILES['sendimage']['size'];
//                $image = $fileName;
            if (empty($fileName)){
                $errorMsg = json_encode(array(
                    'status' => false,
                    'message' => "please select image"
                ));
                echo $errorMsg;
            }else{
                $upload_path = "upload/";

                $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
                $valid_extensions = array('jpeg','jpg','png','gif','webp');

                if (in_array($fileExt,$valid_extensions)){
                    if(!file_exists($upload_path . $fileName)){
                        if ($fileSize < 10000000){
                            move_uploaded_file($temPath, $upload_path . $fileName);
                        }else{
                            $errorMsg = json_encode(array(
                                'status' => false,
                                'message' => "Sorry, your file is too large, please upload 5 MB size"
                            ));
                            echo $errorMsg;
                        }
                    }else{
                        $errorMsg = json_encode(array(
                            'status' => false,
                            'message' => "Sorry, file already exists check upload folder"
                        ));
                        echo $errorMsg;
                    }
                }else{
                    $errorMsg = json_encode(array(
                        'status' => false,
                        'message' => "Sorry, only JPG, JPEG, PNG & GIF files are allowed"
                    ));
                    echo $errorMsg;
                }
            }
            if(!isset($errorMsg)){
                $data = [$name, $price, $quantity, $fileName, $des];
                $data_insert = $this->modelProduct->update($id,$data);
                echo json_encode(array(
                    'status' => true,
                    'message' => "Update Success"
                ));
            }
        }
    }
    public function delete()
    {
        if($_SERVER['REQUEST_METHOD'] == "GET"){
//            $data = file_get_contents("php://input");
//            if (isset($data) && !empty(($data))) {
//                $request = json_decode($data);
//                $id = $request->id;
//                print_r($id);
//                die();
//            $id = isset($_GET['id']) ? $_GET['id'] : die();
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                if (is_numeric($id) == true){
                    if ($this->modelProduct->delete($id)) {
                        echo json_encode(array(
                            'status' => 1,
                            'message' => "Delete Success",
                        ));
                    } else {
                        echo json_encode(array(
                            'status' => 0,
                            'message' => "Delete error"
                        ));
                    }
                }else{
                    echo json_encode(array(
                        'status' => 0,
                        'message' => "not integer"
                    ));
                }
            }

            }
//        }
    }
    public function pagination()
    {
        if($_SERVER['REQUEST_METHOD'] === "GET"){
            if($_GET["page"] && $_GET["row_per_page"]){
                $response = [];
                $page = $_GET['page'];
                $row_per_page = $_GET["row_per_page"];
                $begin = ($page * $row_per_page) - $row_per_page;
                $data = $this->modelProduct->readPaginate($begin, $row_per_page);

                if (count($data) > 0){
                    echo json_encode(array(
                        'status' => 1,
                        'message' => "Success",
                        'Data'=> $data
                    ));
                }else{
                    echo json_encode(array(
                        'status' => 0,
                        'message' => "Not Found"
                    ));
                }
            }else{
                echo json_encode(array(
                    'status' => 0,
                    'message' => "INVALID REQUEST"
                ));
            }
        }
    }
}
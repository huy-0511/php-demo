<?php
namespace App\Controller;
require_once 'Core/Config.php';
require_once 'Core/generate.php';
require_once ('./vendor/autoload.php');
define('SECRET_KEY','Super-Secret-Key');
require_once ('Models/User.php');
use \Firebase\JWT\JWT;
use App\Models\User;
class UserController
{
    private $modelUser;

    public function __construct()
    {
        $this->modelUser = new User();
    }

    public function getData()
    {
        $result = $this->modelUser->get();
        $data_user = array();

        while ($data = $result->fetch_assoc()) {
            $data_user[] = $data;
        }
        echo json_encode(array(
            'data' => $data_user,
            'status' => 1,
            'message' => "Success"
        ));
    }
    public function findOneUser()
    {
        if($_SERVER['REQUEST_METHOD'] == "GET") {
            if(isset($_GET['id'])){
                $id = $_GET['id'];
                if(is_numeric($id)){
                    $result = $this->modelUser->find($id);
                    if($result){
                        echo json_encode(array(
                            'status' => 1,
                            "data"=> $result
                        ));
                    }
                }else{
                    echo json_encode(array(
                        'status' => 1,
                        "message"=> "Not numberic"
                    ));
                }
            }

        }
    }
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $data = file_get_contents('php://input');
            if (isset($data) && !empty(($data))) {
                $request = json_decode($data);
                $name = $request->name;
                $email = $request->email;
                $password = password_hash($request->password,PASSWORD_DEFAULT);

                $check_email = $this->modelUser->check_email($email);
                if(!empty($check_email)){
                    http_response_code(400);
                    echo json_encode(array(
                        'status'=> 0,
                        'message'=> "User already exists, try another email address"
                    ));
                }else{
                    $aa = [$name, $email, $password];
                    if ($this->modelUser->insert($aa)) {
                        http_response_code(200);
                        echo json_encode(array(
                            'status' => 1,
                            'message' => "User has been created"
                        ));
                    } else {
                        http_response_code(500);
                        echo json_encode(array(
                            'status' => 0,
                            'message' => "Create Error"
                        ));
                    }
                }

            } else {
                echo json_encode(array(
                    'status' => 0,
                    'message' => "All data needed"
                ));
            }
        } else {
            echo json_encode(array(
                'status' => 0,
                'message' => "Access Denied"
            ));
        }
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == "PUT"){
            $data = file_get_contents("php://input");
            if (isset($data) && !empty(($data))){
                $request = json_decode($data);
//                var_dump($request);
//                exit();

                if(isset($_GET['id'])){
                    if(is_numeric($_GET['id'])){
                        $id = $request->id;
                        $name = $request->name;
                        $password = password_hash($request->password,PASSWORD_DEFAULT);
                        if($name == ""){
                            echo json_encode(array(
                                'status'=>0,
                                'message'=> "This name is not empty"
                            ));
                            die();
                        }
                        $email = $request->email;
                        if($email == ""){
                            echo json_encode(array(
                                'status'=>0,
                                'message'=> "This email is not empty"
                            ));
                            die();
                        }
                        $check_email = $this->modelUser->check_email($email);
                        if(!empty($check_email)){
                            http_response_code(400);
                            echo json_encode(array(
                                'status'=> 0,
                                'message'=> "Email already exists, try another email address"
                            ));
                        }else{
                            $data_update = [$name,$email,$password];
                            if ($this->modelUser->update($id,$data_update)){
                                http_response_code(200);
                                echo json_encode(array(
                                    'status'=>1,
                                    'message'=> "Update success"
                                ));
                            }else{
                                http_response_code(400);
                                echo json_encode(array(
                                    'status'=>0,
                                    'message'=> "Update error"
                                ));
                            }
                        }
                    }else{
                        echo json_encode(array(
                            'status'=>0,
                            'message'=> "Not numberic"
                        ));
                    }
                }

            }
        }
    }
    public function delete()
    {
       if($_SERVER['REQUEST_METHOD'] == "GET"){
//           $data = file_get_contents("php://input");
//           if (isset($data) && !empty(($data))) {
//               $request = json_decode($data);
//            $id = isset($_GET['id']) ? $_GET['id'] : die();
           if(isset($_GET['id'])){
               $id = $_GET['id'];
               if (is_numeric($id) == true){
                   if ($this->modelUser->delete($id)) {
                       echo json_encode(array(
                           'status' => 1,
                           'message' => "Delete Success"
                       ));
                   } else {
                       echo json_encode(array(
                           'status' => 0,
                           'message' => "Delete error"
                       ));
                   }
               }
           }else{
               echo json_encode(array(
                   'status' => 0,
                   'message' => "Not numeric"
               ));
           }

//           }
       }
    }
    public function login()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST") {
            $data = file_get_contents('php://input');
            if (isset($data) && !empty(($data))) {
                $request = json_decode($data);
                $email = $request->email;
                $password = $request->password;
                $data_user = $this->modelUser->check_login($email);
//                print_r($data_user);
                if (!empty($data_user)){
                    $name_query = $data_user['name'];
                    $email_query = $data_user['email'];
                    $password_query = $data_user['password'];
                    if (password_verify($password,$password_query)){
                        $iss = "localhost"; //tổ chức phát hành token
                        $iat = time(); // thời điểm token được phát hành, tính theo UNIX time
                        $nbf = $iat + 10; //token sẽ chưa hợp lệ trước thời điểm này
                        $exp = $iat + 7200; //thời điểm token sẽ hết hạn
                        $aud = "myuser"; // đối tượng sử dụng token
                        $user_arr_data = array(
                            "id" => $data_user['id'],
                            "name"=> $data_user['name'],
                            "email"=> $data_user['email'],
                        );
                        $secret_key = "owt125";
                        $token = array(
                            "iss" => $iss,
                            "iat" => $iat,
                            "nbf" =>$nbf,
                            "exp" =>$exp,
                            "aud" =>$aud,
                            "data"=>$user_arr_data
                        );
                        $jwt = JWT::encode($token,  SECRET_KEY,'HS512');

                        http_response_code(200);
                        echo json_encode(array(
                            'message'=> "Success Login",
                            'token' => $jwt,
                            'user' =>$user_arr_data
                            
                        ));
                    }else{
                        http_response_code(404);
                        echo json_encode(array(
                            'status'=> 1,
                            'message'=> "Login failed."
                        ));
                    }
                }else{
                    http_response_code(404);
                    echo json_encode(array(
                        'status'=> 0,
                        'message'=> "Something went wrong, please try again later"
                    ));
                }
            }else{
                http_response_code(404);
                echo json_encode(array(
                    'status'=> 0,
                    'message'=> "All data needed"
                ));
            }
        }
    }
    public function read_token_user()
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            $data = file_get_contents('php://input');
            if(isset($data) && !empty($data)){
                $request = json_decode($data);
                if (!empty($request->jwt)){
                    try{
                        $decode_data = JWT::decode($request->jwt, SECRET_KEY, array('HS512'));
                        $user_id = $decode_data->data->id;
                        http_response_code(200);
                        echo json_encode(array(
                            'status' => 1,
                            'message' => "We got JWT Token",
                            'user_data'=> $decode_data,
                            'user_id'=>$user_id,
                            'token'=>$request->jwt
                        ));
                    }catch (Exception $ex){
                        http_response_code(500);
                        echo json_encode(array(
                            'status' => 0,
                            'message' => $ex->getMessage()

                        ));
                    }
                }
            }
        }else{
            echo json_encode(array(
                'status' => 0,
                'message' => "Error"

            ));
        }
    }

//    public function protec()
//    {
//        $secret_key = "YOUR_SECRET_KEY";
//        $jwt = null;
////        $databaseService = new DatabaseService();
////        $conn = $databaseService->getConnection();
//        $data = json_decode(file_get_contents("php://input"));
//        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
//
//        $arr = explode(" ", $authHeader);
//
//
//        /*echo json_encode(array(
//            "message" => "sd" .$arr[1]
//        ));*/
//
//        $jwt = $arr[1];
//
//        if ($jwt) {
//
//            try {
//
//                $decoded = JWT::decode($jwt, SECRET_KEY, array('HS256'));
//
//                // Access is granted. Add code of the operation here
//
//                echo json_encode(array(
//                    "message" => "Access granted:",
//                    "error" => $e->getMessage()
//                ));
//
//            } catch (Exception $e) {
//
//                http_response_code(401);
//
//                echo json_encode(array(
//                    "message" => "Access denied.",
//                    "error" => $e->getMessage()
//                ));
//            }
//        }
//    }
}
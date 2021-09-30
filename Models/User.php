<?php
namespace App\Models;
use App\Core\CoreModel;
require_once ('Core/CoreModel.php');
class User extends CoreModel
{
    protected $table = 'user';
    protected $fillable = [
        'name', 'email', 'password'
    ];
    public function check_login($email){
        $email_query = "SELECT * FROM ".$this->table." WHERE `email` = '$email'";
        if (!empty($this->conn)){
            $result = mysqli_query($this->conn, $email_query);
            if ($result){
                $row=mysqli_fetch_assoc($result);
                return $row;
            }else{
                return false;
            }
        }
    }
    public function check_email($email){
        $email_query = "SELECT * FROM ".$this->table." WHERE `email` = '$email'";
        if (!empty($this->conn)){
            $result = mysqli_query($this->conn, $email_query);
            if ($result){
                $row=mysqli_fetch_assoc($result);
                return $row;
            }else{
                return false;
            }
        }
    }
}
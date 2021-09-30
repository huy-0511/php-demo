<?php
namespace App\Core;
use mysqli;

class Database{
    public $servername = 'ymese.mysql.database.azure.com';
    public $username = 'u_dhuy';
    public $password = 'huy@123';
    public $db = 'dhuy_ymese';
    public $port= 3306;
    public $conn;

    public function __construct()
    {
        $this->connect();
    }
    public function connect()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->db, $this->port);
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
//        echo "success";
        return $this->conn;
    }
}
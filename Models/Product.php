<?php
namespace App\Models;
use App\Core\CoreModel;
require_once ('Core/CoreModel.php');
class Product extends CoreModel
{
    protected $table = "products";
    protected $fillable = [
      'name','price','quantity','image','description'
    ];
    public function readPaginate($begin, $row_per_page)
    {
        $sql = "SELECT * FROM `products` LIMIT {$begin},{$row_per_page}";
        if (!empty($this->conn)){
            $result = mysqli_query($this->conn, $sql);

            if ($result){
                $row=mysqli_fetch_all($result);
                return $row;
            }else{
                return false;
            }
        }
    }
}
<?php
namespace App\Core;
use App\Core\Database;
require_once 'Core/Config.php';
require_once ('Core/Database.php');
class CoreModel extends Database
{
    protected $table;
    protected $fillable = [];

    public function get()
    {
        $sql = "SELECT * FROM ".$this->table." ORDER BY id DESC";

        $select = $this->conn->query($sql);
        if ($select->num_rows > 0) // nhiều hơn 0 bản ghi sẽ lấy ra
        {
            return $select;
        }else{
            return false;
        }
    }

    public function find($id)
    {
        $query = 'SELECT * FROM '.$this->table.' WHERE id ='.$id;;
        $item = mysqli_query($this->conn, $query);

        return mysqli_fetch_assoc($item);
    }

    public function insert($data)
    {
        $colums = '';
        if (isset($this->fillable) && !empty($this->fillable)) {
            foreach ($this->fillable as $key => $colum) {
                $colums .= "`".$colum ."`" . ',';
            }
        }

        $strValue = '';
        foreach ($data as $key => $value) {
            $strValue .= "'".$value ."'" .',';
        }

        $colums = preg_replace('/,$/im', '', $colums);
        $strValue = preg_replace('/,$/im', '', $strValue);

        $insert = "INSERT INTO ". $this->table ."($colums) VALUES($strValue)";

        return $this->conn->query($insert);
    }

    public function delete($id)
    {
        $query = "DELETE FROM ".$this->table." WHERE id =". $id;
//        $query = "DELETE FROM crud WHERE id='$id' ";
        if (!empty($this->conn)){
            if (mysqli_query($this->conn, $query)){
                return true;
            }else{
                return false;
            }
        }
//        return $this->conn->query($query);
    }

    public function update($id, $data)
    {
//        array_push($dataSet, "${key} = '".$value."'");
//        if (isset($this->fillable) && !empty($this->fillable)) {
//            foreach ($this->fillable as $key => $colum) {
//                $colums .= "`".$colum ."`" . ',';
//            }
//        }
        $array = array_combine($this->fillable,$data);
        // print_r($array);
        // die();
        $dataSet = [];
        foreach ($array as $key => $value)
        {
            array_push($dataSet,"$key = '".$value."'");

        }

        $setString = implode(',',$dataSet);

        $update = "UPDATE $this->table SET $setString WHERE id=".$id;
//        print_r($update);
//        die();
        return $this->conn->query($update);

    }
}
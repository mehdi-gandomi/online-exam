<?php
namespace App\Models;

use Core\Model;
use \PDO;

class Professor extends Model
{
    public static function by_username($username,$fields="*")
    {
        try {
            $db=static::getDB();
            $sql="SELECT $fields FROM users WHERE username = '$username' AND user_type = '1'";
            $result=$db->query($sql);
            return $result ? $result->fetch(PDO::FETCH_ASSOC):false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
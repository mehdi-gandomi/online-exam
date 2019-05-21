<?php
namespace App\Models;

use Core\Model;
use \PDO;

class User extends Model
{
    public static function by_username($username,$fields="*")
    {
        try {
            return static::select("users", $fields, ['username' => $username], true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
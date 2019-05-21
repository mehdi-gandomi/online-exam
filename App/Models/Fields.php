<?php
namespace App\Models;

use Core\Model;
use \PDO;

class Fields extends Model
{
    public static function all()
    {
        try{
            return static::select("study_fields");
        }catch (\Exception $e){
            return [];
        }
    }
}
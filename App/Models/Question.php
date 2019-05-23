<?php
namespace App\Models;
use Core\Model;
use \PDO;

class Question extends Model
{
    public static function new($post)
    {
        try{
            static::insert("questions",$post);
            return static::get_last_inserted_id();
        }catch (\Exception $e){
            file_put_contents("err.txt",$e->getMessage());
            return false;
        }
    }

    public static function by_id($qid)
    {
        try{
            return static::select("questions","*",['id'=>$qid],true);
        }catch (\Exception $e){
            return false;
        }
    }
}
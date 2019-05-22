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

    public static function new($post)
    {
        try{
            static::insert("study_fields",[
               'title'=>$post['name']
            ]);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    public static function delete_by_id($fieldId)
    {
        try{
            static::delete("study_fields",['id'=>$fieldId]);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }
}
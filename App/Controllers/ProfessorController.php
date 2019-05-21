<?php
namespace App\Controllers;
use Core\Controller;
use App\Models\Fields;
class ProfessorController extends Controller{
    public function get_study_fields($req,$res,$args)
    {
        $fields=Fields::all();
        return $this->view->render($res,"user/study_fields.twig",['study_fields'=>$fields]);
    }
}
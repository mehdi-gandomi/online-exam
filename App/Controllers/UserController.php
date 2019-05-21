<?php
namespace App\Controllers;
use Core\Controller;
use App\Models\Exam;
class UserController extends Controller{
    public function get_exams_page($req,$res,$args)
    {
        $exams=Exam::all();
        return $this->view->render($res,"user/exams.twig",['exams'=>$exams]);
    }

    public function get_exam_info($req,$res,$args)
    {
        $examInfo=Exam::by_id($args['exam_id']);
        return $examInfo ? $res->withJson(['ok'=>true,'info'=>$examInfo]):['ok'=>false];
    }

    public function get_exam_page($req,$res,$args)
    {
        $questions=Exam::get_questions_by_exam_id($args['exam_id']);
        $examInfo=Exam::by_id($args['exam_id']);
        return $this->view->render($res,"user/exam.twig",['questions'=>$questions,'exam_title'=>$examInfo['name'],'exam_id'=>$args['exam_id']]);
    }

    public function post_save_exam_info($req,$res,$args)
    {
        $answers=$req->getParsedBody();
        $examId=$answers['exam_id'];
        unset($answers['exam_id']);
        $logResult=Exam::create_logs(1,$answers);
        if ($logResult){
            $examResult=Exam::calculate_and_save_exam_result(1,$examId,$answers);
            if ($examResult){
                return $this->view->render($res,"user/exam_result.twig",$examResult);
            }
        }
    }
}
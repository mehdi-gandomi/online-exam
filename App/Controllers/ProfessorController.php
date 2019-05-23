<?php
namespace App\Controllers;
use App\Models\Professor;
use Core\Controller;
use App\Models\Fields;
use App\Models\Exam;
use App\Models\Question;
class ProfessorController extends Controller{
    public function get_study_fields($req,$res,$args)
    {
        $fields=Fields::all();
        return $this->view->render($res,"professor/study_fields.twig",['study_fields'=>$fields]);
    }
    public function get_login_page($req,$res,$args)
    {
        $data = [];
        if (isset($_SESSION['is_user_logged_in'])) {
            return $res->withRedirect("/professor");
        }
        if (isset($_SESSION['login_username'])) {
            $data = array_merge($data, ["login_username" => $_SESSION['login_username']]);
            unset($_SESSION['login_username']);
        }
        if (isset($_SESSION['oldSignUpFields'])) {
            $data = array_merge($data, $_SESSION['oldSignUpFields']);
            unset($_SESSION['oldSignUpFields']);
        }
        return $this->view->render($res,"professor/login.twig",$data);
    }
    //login process of user (customer) if user is not active, an activation link will be sent to user's email
    public function post_login($req, $res, $args)
    {
        $postFields = $req->getParsedBody();
        $userData = Professor::by_username($postFields['username'], "*");
        if ($userData) {
            if ($userData['password'] === \md5(\md5($postFields['password']))) {
                $_SESSION['is_professor_logged_in'] = true;
                $_SESSION['fname'] = $userData['fname'];
                $_SESSION['lname'] = $userData['lname'];
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_type'] = "professor";
                return $res->withRedirect('/professor');
            } else {
                $this->flash->addMessage('userLoginError', "پسورد وارد شده صحیح نمیباشد!");
                $_SESSION['login_username'] = $postFields['username'];
                return $res->withRedirect('/professor/login');
            }
        } else {
            $this->flash->addMessage('userLoginError', "نام کاربری وارد شده صحیح نمی باشد !");
            $_SESSION['login_username'] = $postFields['username'];
            return $res->withRedirect('/professor/login');
        }

    }

    //logout process for user
    public function logout($req, $res, $args)
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['is_professor_logged_in']);
        unset($_SESSION['fname']);
        unset($_SESSION['lname']);
        unset($_SESSION['user_type']);
        unset($_COOKIE[\session_name()]);
        return $res->withRedirect('/');
    }

    public function new_study_field($req,$res,$args)
    {
        $body=$req->getParsedBody();
        $result=Fields::new($body);
        return $result ? $res->withJson(['ok'=>true]):$res->withJson(['ok'=>false]);
    }

    public function delete_study_field($req,$res,$args)
    {
        $result=Fields::delete_by_id($args['field_id']);
        return $result ? $res->withJson(['ok'=>true]):$res->withJson(['ok'=>false]);
    }

    public function new_exam($req,$res,$args)
    {
        $fields=Fields::all();
        return $this->view->render($res,"professor/new-exam.twig",['study_fields'=>$fields]);
    }

    public function post_new_exam($req,$res,$args)
    {
        $body=$req->getParsedBody();
        $examId=Exam::new($body);
        if ($examId){
            return $res->withRedirect("/professor/new-exam/questions/new?exam_id=$examId");
        }
    }

    public function new_exam_questions($req,$res,$args)
    {
        $examId=$req->getQueryParam("exam_id");
        $examInfo=Exam::by_id($examId);
        if ($examInfo){
            $data=['exam_info'=>$examInfo];
            $questions=Exam::get_all_questions_by_id($examId);
            if ($questions && count($questions) == $examInfo['question_count']){
                $data['is_full']=true;
            }
            return $this->view->render($res,"professor/add-questions.twig",$data);
        }
        echo "چنین آزمونی یافت نشد";
    }

    public function save_exam_question($req,$res,$args)
    {
        $body=$req->getParsedBody();
        $result=Question::new($body);
        return $result ? $res->withJson(['ok'=>true,'info'=>['id'=>$result,'name'=>$body['title']]]):$res->withJson(['ok'=>false]);
    }

    public function get_question_info($req,$res,$args)
    {
        $qid=$req->getParam("qid");
        $questionInfo=Question::by_id($qid);
        return $questionInfo ? $res->withJson(['ok'=>true,'info'=>$questionInfo]):$res->withJson(['ok'=>false]);
    }

    public function get_exams_page($req,$res,$args)
    {
        $exams=Exam::all();
        return $this->view->render($res,"professor/exams.twig",['exams'=>$exams]);
    }

    public function delete_exam($req,$res,$args)
    {
        $result=Exam::delete_by_id($args['exam_id']);
        return $result ? $res->withJson(['ok'=>true]):$res->withJson(['ok'=>false]);
    }
    public function get_exam_info($req,$res,$args)
    {
        $examInfo=Exam::by_id($args['exam_id']);
        return $examInfo ? $res->withJson(['ok'=>true,'info'=>$examInfo]):['ok'=>false];
    }

    public function get_exam_questions_page($req,$res,$args)
    {
        $questions=Exam::get_questions_by_exam_id($args['exam_id']);
        $examInfo=Exam::by_id($args['exam_id']);
        if ($questions){
            return $this->view->render($res,"professor/show-exam-questions.twig",['questions'=>$questions,'exam_title'=>$examInfo['name']]);
        }
        echo "چنین آزمونی یافت نشد !";
    }

    public function get_exams_results_page($req,$res,$args)
    {
        $results=Exam::get_all_exams_results();
        return $this->view->render($res,"professor/exams-results.twig",['results'=>$results]);
    }

    public function get_exam_result_json($req,$res,$args)
    {
        $result=Exam::exam_result_by_id($args['exam_id']);
        return $result ? $res->withJson(['ok'=>true,'info'=>$result]):$res->withJson(['ok'=>false]);
    }
}
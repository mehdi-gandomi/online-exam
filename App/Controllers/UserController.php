<?php
namespace App\Controllers;
use Core\Controller;
use App\Models\Exam;
use App\Models\User;
class UserController extends Controller{
    public function get_exams_page($req,$res,$args)
    {
        $exams=Exam::all($_SESSION['user_id']);
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
        $logResult=Exam::create_logs($_SESSION['user_id'],$answers);
        if ($logResult){
            $examResult=Exam::calculate_and_save_exam_result($_SESSION['user_id'],$examId,$answers);
            if ($examResult){
                return $this->view->render($res,"user/exam_result.twig",$examResult);
            }
        }
    }

    public function get_exams_results($req,$res,$args)
    {
        $exams=Exam::get_completed_exams_by_user_id($_SESSION['user_id']);
        return $this->view->render($res,"user/exams_results.twig",['exams'=>$exams]);
    }

    public function get_exam_results_json($req,$res,$args)
    {
        $examInfo=Exam::get_results_info_by_id($args['exam_id']);
        return $examInfo ? $res->withJson(['ok'=>true,'info'=>$examInfo]):['ok'=>false];
    }

    public function get_login_page($req,$res,$args)
    {
        $data = [];
        if (isset($_SESSION['is_user_logged_in'])) {
            return $res->withRedirect("/user");
        }
        if (isset($_SESSION['login_username'])) {
            $data = array_merge($data, ["login_username" => $_SESSION['login_username']]);
            unset($_SESSION['login_username']);
        }
        if (isset($_SESSION['oldSignUpFields'])) {
            $data = array_merge($data, $_SESSION['oldSignUpFields']);
            unset($_SESSION['oldSignUpFields']);
        }
        return $this->view->render($res,"user/login.twig",$data);
    }
    //login process of user (customer) if user is not active, an activation link will be sent to user's email
    public function post_login($req, $res, $args)
    {
        $postFields = $req->getParsedBody();
        $userData = User::by_username($postFields['username'], "*");
        if ($userData) {
            if ($userData['password'] === \md5(\md5($postFields['password']))) {
                $_SESSION['is_user_logged_in'] = true;
                $_SESSION['fname'] = $userData['fname'];
                $_SESSION['lname'] = $userData['lname'];
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_type'] = "user";
                return $res->withRedirect('/user');
            } else {
                $this->flash->addMessage('userLoginError', "پسورد وارد شده صحیح نمیباشد!");
                $_SESSION['login_username'] = $postFields['username'];
                return $res->withRedirect('/user/login');
            }
        } else {
            $this->flash->addMessage('userLoginError', "نام کاربری وارد شده صحیح نمی باشد !");
            $_SESSION['login_username'] = $postFields['username'];
//            return $res->withRedirect('/user/login');
        }

    }

    //logout process for user
    public function logout($req, $res, $args)
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['is_user_logged_in']);
        unset($_SESSION['fname']);
        unset($_SESSION['lname']);
        unset($_SESSION['user_type']);
        unset($_COOKIE[\session_name()]);
        return $res->withRedirect('/');
    }

    public function get_question_answers_page($req,$res,$args)
    {
        $questions=Exam::get_user_question_answers_by_exam_id($args['exam_id'],$_SESSION['user_id']);
        $examInfo=Exam::by_id($args['exam_id']);
        return $this->view->render($res,"user/question-answers.twig",['questions'=>$questions,'exam_title'=>$examInfo['name']]);
    }
}
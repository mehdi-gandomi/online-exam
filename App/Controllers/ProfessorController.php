<?php
namespace App\Controllers;
use App\Models\Professor;
use Core\Controller;
use App\Models\Fields;
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
        var_dump($userData);
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
//            return $res->withRedirect('/professor/login');
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
}
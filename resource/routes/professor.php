<?php
use Slim\Http\Request;
use Slim\Http\Response;
$container = $app->getContainer();
$authMV = function ($req, $res, $next) use ($container) {
    if (isset($_SESSION['is_professor_logged_in'])) {
        return $next($req, $res);
    } else {
        return $res->withRedirect("/professor/login");
    }

};


$app->group('/professor', function ($app) use ($container) {
    $app->get('', function (Request $request, Response $response, array $args) {
        $this->view->render($response, "professor/dashboard.twig", ["title" => "صفحه اصلی"]);
    });
    $app->get('/study-fields', "App\Controllers\ProfessorController:get_study_fields");
    $app->get('/exam/new', "App\Controllers\ProfessorController:new_exam");
    $app->post('/study-field/new', "App\Controllers\ProfessorController:new_study_field");
    $app->delete('/study-field/{field_id}', "App\Controllers\ProfessorController:delete_study_field");
    $app->post('/exam/new', "App\Controllers\ProfessorController:post_new_exam");
    $app->get('/new-exam/questions/new', "App\Controllers\ProfessorController:new_exam_questions");
    $app->get('/question/info/json', "App\Controllers\ProfessorController:get_question_info");
    $app->get('/exams', "App\Controllers\ProfessorController:get_exams_page");
    $app->delete('/exam/{exam_id}', "App\Controllers\ProfessorController:delete_exam");
    $app->post("/new-exam/question/save", "App\Controllers\ProfessorController:save_exam_question");
    $app->get('/exam/json/{exam_id}', "App\Controllers\ProfessorController:get_exam_info");
    $app->get('/exam/questions/show/{exam_id}', "App\Controllers\ProfessorController:get_exam_questions_page");

})->add($authMV);
$app->get('/professor/login', "App\Controllers\ProfessorController:get_login_page");
$app->post('/professor/login', "App\Controllers\ProfessorController:post_login");
$app->get('/professor/logout', "App\Controllers\ProfessorController:logout");
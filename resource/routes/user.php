<?php
use Slim\Http\Request;
use Slim\Http\Response;
$container = $app->getContainer();
$authMV = function ($req, $res, $next) use ($container) {
    if (isset($_SESSION['is_user_logged_in'])) {
        return $next($req, $res);
    } else {
        return $res->withRedirect("/user/login");
    }

};


$app->get('/', function (Request $request, Response $response, array $args) {
    $this->view->render($response, "index.twig", ["page_title" => "صفحه اصلی"]);
});
$app->group('/user', function ($app) use ($container) {
    $app->get('', function (Request $request, Response $response, array $args) {
        $this->view->render($response, "user/dashboard.twig", ["page_title" => "صفحه اصلی"]);
    });
    $app->get('/exams', "App\Controllers\UserController:get_exams_page");
    $app->get('/exam/json/{exam_id}', "App\Controllers\UserController:get_exam_info");
    $app->get('/exam/{exam_id}', "App\Controllers\UserController:get_exam_page");
    $app->get('/exams/results', "App\Controllers\UserController:get_exams_results");
    $app->get('/exam/results/json/{exam_id}', "App\Controllers\UserController:get_exam_results_json");
    $app->get('/exam/question-answers/{exam_id}', "App\Controllers\UserController:get_question_answers_page");

    $app->post('/exam/save', "App\Controllers\UserController:post_save_exam_info");
})->add($authMV);
$app->get('/user/login', "App\Controllers\UserController:get_login_page");
$app->post('/user/login', "App\Controllers\UserController:post_login");
$app->get('/user/logout', "App\Controllers\UserController:logout");
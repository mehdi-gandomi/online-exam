<?php
use Slim\Http\Request;
use Slim\Http\Response;
$container = $app->getContainer();


$app->group('/user', function ($app) use ($container) {
    $app->get('', function (Request $request, Response $response, array $args) {
        $this->view->render($response, "user/dashboard.twig", ["page_title" => "صفحه اصلی"]);
    });
    $app->get('/exams', "App\Controllers\UserController:get_exams_page");
    $app->get('/exam/json/{exam_id}', "App\Controllers\UserController:get_exam_info");
    $app->get('/exam/{exam_id}', "App\Controllers\UserController:get_exam_page");
    $app->post('/exam/save', "App\Controllers\UserController:post_save_exam_info");
});

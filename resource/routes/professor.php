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
})->add($authMV);
$app->get('/professor/login', "App\Controllers\ProfessorController:get_login_page");
$app->post('/professor/login', "App\Controllers\ProfessorController:post_login");
$app->get('/professor/logout', "App\Controllers\ProfessorController:logout");
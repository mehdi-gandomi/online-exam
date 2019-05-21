<?php
use Slim\Http\Request;
use Slim\Http\Response;
$container = $app->getContainer();


$app->group('/professor', function ($app) use ($container) {
    $app->get('', function (Request $request, Response $response, array $args) {
        $this->view->render($response, "professor/dashboard.twig", ["page_title" => "صفحه اصلی"]);
    });
    $app->get('/study-fields', "App\Controllers\ProfessorController:get_study_fields");
});

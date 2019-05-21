<?php
use Slim\Http\Request;
use Slim\Http\Response;
$container = $app->getContainer();


$app->group('/user', function ($app) use ($container) {
    $app->get('', function (Request $request, Response $response, array $args) {
        $this->view->render($response, "user/dashboard.twig", ["page_title" => "صفحه اصلی"]);
    });
    $app->get('payment-success/{order_number}', "App\Controllers\OrderController:payment_result_zarinpal");
});

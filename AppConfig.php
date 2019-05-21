<?php


spl_autoload_register(function ($class) {
    // if(substr($class, -10) === "Controller"){
    //     $file=__DIR__.'\\App\\Controllers\\'.$class.".php";
    // }else{

    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    // }
    if (is_readable($file)) {

        require $file;
    }
});
// var_dump($_SESSION);
// die();
// Create and configure Slim app
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
    'csrf' => function ($c) {
        return new \Slim\Csrf\Guard;
    },
    'view' => function ($c) {
        $view = new \Slim\Views\Twig('resource/views');

        // Instantiate and add Slim specific extension
        $router = $c->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        $view->addExtension(new Knlv\Slim\Views\TwigMessages(
            new Slim\Flash\Messages()
        ));
        $view->getEnvironment()->addGlobal("user", array(
            'is_logged_in' => isset($_SESSION['is_user_logged_in']) || isset($_SESSION['is_translator_logged_in']) || isset($_SESSION['is_admin_logged_in']),
            'fname' => isset($_SESSION['fname']) ? $_SESSION['fname'] : false,
            'lname'=>isset($_SESSION['lname']) ? $_SESSION['lname']:false,
            'user_type' => isset($_SESSION['user_type']) ? $_SESSION['user_type'] : false,
            'user_id'=>isset($_SESSION['user_id']) ? $_SESSION['user_id']:0
        ));
        return $view;
    },
    'notFoundHandler' => function ($c) {
        return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($c) {
            $route = $_SERVER['REQUEST_URI'];
            
            if (strpos($route, "user/") || strpos($route, "user") || strpos($route, "translator/") || strpos($route, "translator") || strpos($route, "admin/") || strpos($route, "admin")) {
                $view = $c->get('view');
                $view->render($response, '404.twig');
            } else {    
                $view = $c->get('view');
                $view->render($response, '404.twig');
            }

            return $response;
        };
    },

];
$c = new \Slim\Container($configuration);
// Register provider
$c['flash'] = function () {
    return new \Slim\Flash\Messages();
};

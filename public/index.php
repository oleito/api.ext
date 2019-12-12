<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/classes/token.php';
require '../src/classes/peticion.php';
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

#header("Allow: GET, POST, PUT, DELETE");

/*
$config['db']['host']   = 'localhost';
$config['db']['user']   = 'user';
$config['db']['pass']   = 'password';
$config['db']['dbname'] = 'exampleapp';
(['settings' => $config])
 */

$app = new \Slim\App;

################### RAIZ ###########################

/**
 * GET
 */
$app->get('/', function (Request $request, Response $response, array $args) {
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API
        $resp = array(
            'respuesta' => array(
                'nombre' => 'leandro',
                'apellido' => 'ortega',
            ),
        );

        return $resp;
    };

    return $peticiones->conTokenGet($func($request));
});

/**
 * POST
 */
$app->post('/', function (Request $request, Response $response, array $args) {

    $bodyIn = [];
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API

        $bodyIn = $request->getParsedBody();
        $bodyOut = $bodyIn;

        return $bodyOut;
    };

    return $peticiones->conTokenPost($func($request), true, null);
});

/**
 * PUT
 */
$app->put('/', function (Request $request, Response $response, array $args) {
    $bodyIn = [];
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API

        $bodyIn = $request->getParsedBody();
        $bodyOut = $bodyIn;

        return $bodyOut;
    };

    return $peticiones->conTokenPost($func($request), true, null);
});

########################## END ##########################

$app->post('/login', function (Request $request, Response $response, array $args) {

    $bodyIn = [];
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $bodyIn = $request->getParsedBody();
    $userName = $bodyIn['user']['userName'];
    $userPassword = $bodyIn['user']['userPassword'];

    function verificarUsuario($userName, $userPassword)
    {
        //Verifia si las credenciales enviadas son correctas.
        //Conectarse a la base de datos,
        if ($userName === 'oleito' && $userPassword === '1234') {
            return true;
        }
        return false;
    };

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API

        $bodyIn = $request->getParsedBody();
        //$bodyOut = $bodyIn;
        $bodyOut = [];

        return $bodyOut;
    };

    if (verificarUsuario($userName, $userPassword)) {
        return $peticiones->sinTokenLogin($func($request), true, null);
    } else {
        return $peticiones->sinTokenLogin($func($request), false, 401);
    }

});

$app->run();


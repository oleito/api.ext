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

/**
 * DELETE
 */

$app->delete('/{id}', function (Request $request, Response $response, array $args) {
    $bodyIn = [];
    $bodyOut = [];
    $id = $args['id'];

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request, $id) {
        #logica de la funcion, entrega el arreglo a devolver por la API
        $bodyIn = $request->getParsedBody();
        $bodyOut = 'El elemento ' . $id . ' fue borrado.';

        return $bodyOut;
    };

    return $peticiones->conTokenPost($func($request, $id), true, null);
});
########################## END ##########################

$app->run();

<?php

// Permite la conexion desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Permite la ejecucion de los metodos
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");


use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/classes/token.php';
require '../src/classes/peticion.php';
require '../src/classes/mysql.php';
require '../src/classes/filtro.php';
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = true;


$app = new \Slim\App(['settings' => $config]);

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
                'apellido' => 'nicolas',
            ),
        );

        return $resp;
    };

    return $peticiones->sinTokenGet($func($request), true, null);
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

    return $peticiones->sinTokenPost($func($request), true, null);
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

########################## RUTAS ##########################


require_once '../src/rutas/login.php';
require_once '../src/rutas/marca.php';
require_once '../src/rutas/modelo.php';
require_once '../src/rutas/seguro.php';
require_once '../src/rutas/tipo.php';
require_once '../src/rutas/traza.php';


$app->run();

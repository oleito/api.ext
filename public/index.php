<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/classes/token.php';
require '../src/classes/peticion.php';
require '../src/classes/mysql.php';
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

    $usuario_nombre = null;
    $usuario_apellido = null;

    function verificarUsuario($userName, $userPassword)
    {
        $mysql = new mysql;
        if ($mysql->conectar()) {
            $savedUserData = $mysql->buscar('usuario', "usuario_username = '$userName'");

            if ($bodyOut['logIn'] = password_verify($userPassword, $savedUserData[0]['usuario_password'])) {
                $GLOBALS['usuario_nombre'] = $savedUserData[0]['usuario_nombre'];
                $GLOBALS['usuario_apellido'] = $savedUserData[0]['usuario_apellido'];
                return true;
            }
        }
        return false;
    };

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API

        $bodyIn = $request->getParsedBody();
        //$bodyOut = $bodyIn;
        // $bodyOut['nombre'] = $GLOBALS['usuario_nombre'];
        $bodyOut['usuario'] = [
            'nombre' => $GLOBALS['usuario_nombre'],
            'apellido' => $GLOBALS['usuario_apellido'],
        ];

        // //////////
        // $mysql = new mysql;
        // if ($mysql->conectar()) {

        //     $password = '1q2w3eparisNadarisca32';

        //     $bodyOut['dbMsj'] = 'parece que todo OK';
        //     //$bodyOut['Insertar'] = $mysql->insertar("usuario", "null, 'usr@dom'");

        //     //  $bodyOut['update'] = $mysql->actualizar("usuario", "
        //     //      usuario_username = 'juan.quiroga@parisautos.com.ar' ,
        //     //      usuario_password = 'soyUnaClave',
        //     //      usuario_nombre = 'Juan Pablo',
        //     //      usuario_apellido = 'Quiroga'
        //     //      ", "idusuario = 4");
        //     //$newPass = password_hash($password, PASSWORD_BCRYPT);

        //     //$bodyOut['update'] = $mysql->actualizar("usuario", "usuario_password = '$newPass'", "idusuario = 1");

        //     //$bodyOut['buscar'] = $mysql->buscar('usuario', 'idusuario > 0');
        //     // $aux = $mysql->buscar('usuario', "usuario_username = 'sistemas@parisautos.com.ar'");

        //     // $bodyOut['buscar'] = $aux[0]['usuario_password'];
        //     // $bodyOut['logIn'] = password_verify($password, $aux[0]['usuario_password']);

        //     //$bodyOut['borrar'] = $mysql->borrar('usuario', 'idusuario = 3');

        //     $bodyOut['Listar'] = $mysql->listar('usuario');
        // } else {
        //     $bodyOut['dbMsj'] = 'parece que todo mal';
        // }
        //////////

        return $bodyOut;
    };

    if (verificarUsuario($userName, $userPassword)) {

        return $peticiones->sinTokenLogin($func($request), true, null);
    } else {
        return $peticiones->sinTokenLogin($func($request), false, 401);
    }

});

$app->run();

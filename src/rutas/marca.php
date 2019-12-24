<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/marca', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {

        $bodyOut = [];

        try {
            $mysql = new mysql;
            $mysql->conectar();
            $bodyOut = $mysql->listar('vhMarca');

            return $bodyOut;
        } catch (\Throwable $th) {
            return false;
        }
    };

    return $peticiones->conTokenGet($func($request), true, null);
});

/**
 * POST
 */
$app->post('/marca', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #logica de la funcion, entrega el arreglo a devolver por la API
        $filtro=new filtro;
        $bodyIn = [];
        $bodyOut = [];

        $bodyIn = $request->getParsedBody();

        if (@$bodyIn['marca'] && @$bodyIn['marca']['marcaNombre'] && @$bodyIn['marca']['marcaIniciales']) {

            
            $nombre = $filtro->stringFilter($bodyIn['marca']['marcaNombre']);
            $iniciales = $filtro->stringFilter($bodyIn['marca']['marcaIniciales']);
            $mysql = new mysql;

            if ($mysql->conectar() && $mysql->insertar("vhMarca", "null, '" . $nombre . "', '" . $iniciales . "'")) {
                $bodyOut = $bodyIn;
            } else {
                return false;
            }
        }
        return $bodyOut;
    };

    return $peticiones->conTokenPost($func($request), true, null);

});

$app->put('/marca', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/marca', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

// //////////
// $mysql = new mysql;
// if ($mysql->conectar()) {

//     $password = '1q2w3eparisNadarisca32';
// $newPass = password_hash($password, PASSWORD_BCRYPT);

// $bodyOut['update'] = $mysql->actualizar("usuario", "usuario_password = '$newPass'", "idusuario = 1");
//     $bodyOut['dbMsj'] = 'parece que todo OK';
//     //$bodyOut['Insertar'] = $mysql->insertar("usuario", "null, 'usr@dom'");

//     //  $bodyOut['update'] = $mysql->actualizar("usuario", "
//     //      usuario_username = 'juan.quiroga@parisautos.com.ar' ,
//     //      usuario_password = 'soyUnaClave',
//     //      usuario_nombre = 'Juan Pablo',
//     //      usuario_apellido = 'Quiroga'
//     //      ", "idusuario = 4");

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

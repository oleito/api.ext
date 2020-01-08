<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/login', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

############
$app->post('/loginTEST', function (Request $request, Response $response, array $args) {

    $bodyIn = [];
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        $bodyIn = $request->getParsedBody();
        
        
         try {
            $DB = new mysqli(
                'localhost',
                'parisaut_orionusr',
                'Be1sIByM7HR@',
                'parisaut_chapa'
            ) or die(mysql_error());
            $DB->set_charset("utf8");

            if (true) {
                
                
                $query = "select * FROM parisaut_chapa.usuario WHERE usuario_username = 'sistemas@parisautos.com.ar' LIMIT 1 ";
                //echo $query;
                $resultado = $DB->query($query);
                //echo ("SELECT * FROM $tabla WHERE $condicion LIMIT 1");
          
                $bodyOut = $resultado->fetch_assoc();

        
                return $bodyOut;
            }
            $bodyOut = $bodyIn;

        } catch (\Throwable $th) {
            return $bodyOut;
        }

        $bodyIn = $request->getParsedBody();
        $bodyOut = $bodyIn;
        return $bodyOut;
    };

    return $peticiones->sinTokenLogin($func($request), true, null);

});

################

$app->post('/login', function (Request $request, Response $response, array $args) {

    $bodyIn = [];
    $bodyOut = [];

    $peticiones = new peticion($request, $response, $args);

    $bodyIn = $request->getParsedBody();
    @$userName = $bodyIn['user']['userName'];
    @$userPassword = $bodyIn['user']['userPassword'];

    $usuario_nombre = '';
    $usuario_apellido = '';

    function verificarUsuario($userName, $userPassword)
    {
        $mysql = new mysql;
        if ($mysql->conectar() && $savedUserData = $mysql->buscar('usuario', "usuario_username = '$userName'")) {

            // $password = 'aleja3022';
            // $newPass = password_hash($password, PASSWORD_BCRYPT);
            // $bodyOut['update'] = $mysql->actualizar("usuario", "usuario_password = '$newPass'", "idusuario = 2");

            if ($savedUserData[0]['usuario_password'] && password_verify($userPassword, $savedUserData[0]['usuario_password'])) {
                $GLOBALS['usuario_nombre'] = $savedUserData[0]['usuario_nombre'];
                $GLOBALS['usuario_apellido'] = $savedUserData[0]['usuario_apellido'];
                $GLOBALS['idusuario'] = $savedUserData[0]['idusuario'];
                return true;
            }
        }
        return false;
    };

    $func = function ($request) {

        $bodyIn = $request->getParsedBody();
        $bodyOut['usuario'] = [
            @'id' => $GLOBALS['idusuario'],
            @'nombre' => $GLOBALS['usuario_nombre'],
            @'apellido' => $GLOBALS['usuario_apellido'],
        ];
        return $bodyOut;
    };

    $funcTest = function ($request) {

        $bodyIn = $request->getParsedBody();
        $bodyOut = $bodyIn;
        return $bodyOut;
    };

    if (verificarUsuario($userName, $userPassword)) {
        return $peticiones->sinTokenLogin($func($request), true, null);
    } else {
        return $peticiones->sinTokenLogin($funcTest($request), false, 401);
    }

});

$app->put('/login', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/login', function (Request $request, Response $response, array $args) {
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

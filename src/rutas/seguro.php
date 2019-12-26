<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/seguro', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {

        $bodyOut = [];

        try {
            $mysql = new mysql;
            $mysql->conectar();
            $bodyOut = $mysql->listar('seguro');

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
$app->post('/seguro', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #### Variables y Clases ####
        $filtro = new filtro;
        $bodyIn = [];
        $bodyOut = [];

        $bodyIn = $request->getParsedBody();

        if (@$bodyIn['seguro'] && @$bodyIn['seguro']['seguroNombre']) {

            $nombre = $filtro->stringFilter($bodyIn['seguro']['seguroNombre']);
            
            $mysql = new mysql;

            if ($mysql->conectar() && $mysql->insertar("seguro", "null, '" . $nombre . "'")) {
                $bodyOut = $bodyIn;
            } else {
                return false;
            }
        }
        
        return $bodyOut;
    };
    return $peticiones->conTokenPost($func($request), true, null);
});

$app->put('/seguro', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/seguro', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});


// piezas like array
// INSERT INTO `seguro` (`idseguro`, `seguro`) VALUES (NULL, 'La Caja'), (NULL, 'Triunfo');  
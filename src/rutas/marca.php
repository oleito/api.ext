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
        #### Variables y Clases ####
        $filtro = new filtro;
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

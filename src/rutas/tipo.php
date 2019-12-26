<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/tipo', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        $filtro = new filtro;
        $bodyOut = [];

        try {
            $mysql = new mysql;
            $mysql->conectar();

            if ($params = $request->getQueryParams()) {
                if ($params['marca']) {
                    $idMarca = $filtro->stringFilter($params['marca']);
                    // return $mysql->listar("vhtipo WHERE vhMarca_idvhMarca = $idMarca");
                }
            }

            return $mysql->listar('vhTipo');

        } catch (\Throwable $th) {
            return false;
        }
    };

    return $peticiones->conTokenGet($func($request), true, null);
});

/**
 * POST
 */

$app->post('/tipo', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->put('/tipo', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/tipo', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

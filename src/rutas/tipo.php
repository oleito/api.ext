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
                if (@$params['modelo']) {
                    $modelo = $filtro->stringFilter($params['modelo']);
                    return $mysql->listar("vhModelo
                    JOIN vhTipo
                    on vhModelo.vhTipo_idvhTipo = vhTipo.idvhTipo
                    WHERE vhModelo.idvhModelo = $modelo");
                } else if (@$params['id']) {
                    $id = $filtro->stringFilter($params['id']);
                    return $mysql->listar("vhTipo WHERE idvhTipo = $id");
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

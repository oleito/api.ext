<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/modelo', function (Request $request, Response $response, array $args) {

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
                    return $mysql->listar("vhModelo WHERE vhMarca_idvhMarca = $idMarca");
                }
            }

            return $mysql->listar('vhModelo');

        } catch (\Throwable $th) {
            return false;
        }
    };

    return $peticiones->conTokenGet($func($request), true, null);
});

/**
 * POST
 */

$app->post('/modelo', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #### Variables y Clases ####
        $filtro = new filtro;
        $bodyIn = [];
        $bodyOut = [];

        $bodyIn = $request->getParsedBody();

        if (@$bodyIn['modelo'] && @$bodyIn['modelo']['modeloNombre'] && @$bodyIn['modelo']['modeloMarca'] && @$bodyIn['modelo']['modeloTipo']) {

            $nombre = $filtro->stringFilter($bodyIn['modelo']['modeloNombre']);
            $marca = $filtro->stringFilter($bodyIn['modelo']['modeloMarca']);
            $tipo = $filtro->stringFilter($bodyIn['modelo']['modeloTipo']);
            //modeloTipo

            $mysql = new mysql;

            // if (true) {
            if ($mysql->conectar() && $mysql->insertar("vhModelo", "null, '" . $nombre . "', '" . $marca . "', '".$tipo."'")) {
                $bodyOut = $bodyIn;
                //INSERT INTO `vhModelo` (`idvhModelo`, `vhModelo`, `vhMarca_idvhMarca`, `vhTipo_idvhTipo`) VALUES (NULL, 'Palio', '9', '1');
            } else {
                return false;
            }
        }

        return $bodyOut;
    };
    return $peticiones->conTokenPost($func($request), true, null);
});

$app->put('/modelo', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/modelo', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

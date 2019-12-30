<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/traza', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        // $filtro = new filtro;
        $bodyOut = [];

        try {
            $mysql = new mysql;
            $mysql->conectar();

            $seguimientos = $mysql->listarCols(
                'idtraza,orden_idorden,vehiculo_patente,vhModelo,vhMarca,seguro',
                'traza
            JOIN vehiculo ON traza.vehiculo_idvehiculo= vehiculo.idvehiculo
            JOIN vhModelo on vehiculo.vhModelo_idvhModelo= vhModelo.idvhModelo
            JOIN vhMarca on vhMarca.idvhMarca= vhModelo.vhMarca_idvhMarca
            JOIN seguro on seguro.idseguro= traza.seguro_idseguro');

            foreach ($seguimientos as $key => $value) {
                // valor del ID de traza
                $idTraza = $value['idtraza'];
                $ultimoMovimiento = $mysql->listarCols(
                    "movimiento_fecha, movimiento_hora, chSector",
                    "movimiento
                    JOIN chSector ON movimiento.chSector_idchSector_destino = chSector.idchSector
                    WHERE movimiento.traza_idtraza = $idTraza ORDER BY movimiento.idmovimiento DESC LIMIT 1"
                );

                $filaConcatenada = array_merge($value, $ultimoMovimiento[0]);

                array_push($bodyOut, $filaConcatenada);
            }
            return $bodyOut;
            //return $seguimientos;

        } catch (\Throwable $th) {
            return false;
        }
    };

    return $peticiones->conTokenGet($func($request), true, null);
});

/**
 * POST
 */

$app->post('/traza', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        #### Variables y Clases ####
        $filtro = new filtro;

        $bodyIn = [];
        $bodyOut = [];

        $bodyIn = $request->getParsedBody();

        if (@$bodyIn['seguimiento']
            && @$bodyIn['seguimiento']['marca']
            && @$bodyIn['seguimiento']['modelo']
            && @$bodyIn['seguimiento']['patente']
            && @$bodyIn['seguimiento']['seguro']
            && @$bodyIn['seguimiento']['orden']
            && @$bodyIn['seguimiento']['fechaIngreso']
            && @$bodyIn['seguimiento']['fechaSalidaAprox']
            && @$bodyIn['seguimiento']['observaciones']
        ) {

            // $nombre = $filtro->stringFilter($bodyIn['modelo']['modeloNombre']);
            // $marca = $filtro->stringFilter($bodyIn['modelo']['modeloMarca']);
            // $tipo = $filtro->stringFilter($bodyIn['modelo']['modeloTipo']);
            //trazaTipo


            ###################
            HAY QUE MODIFICAR LA BASE DE DATOS SI O SI
            ####################

            /**
             * ​INSERT INTO `movimiento` (`idmovimiento`, `traza_idtraza`, `usuario_idusuario`, `chSector_idchSector_origen`, `chSector_idchSector_destino`, `movimiento_fecha`, `movimiento_hora`) VALUES (NULL, '2', '1', '3', '4', '2019-12-30', '01:27:00');
             */

            $mysql = new mysql;

            if (true) {
                //if ($mysql->conectar() && $mysql->insertar("vhModelo", "null, '" . $nombre . "', '" . $marca . "', '".$tipo."'")) {
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

$app->put('/traza', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

$app->delete('/traza', function (Request $request, Response $response, array $args) {
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(405);
});

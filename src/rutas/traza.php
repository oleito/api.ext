<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/traza', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
        $bodyOut = [];

        try {
            $mysql = new mysql;
            $mysql->conectar();

            $seguimientos = $mysql->listarCols(
                'idtraza,orden_idorden,traza_patente,vhModelo,vhMarca,seguro',
                'traza
                JOIN vhModelo on traza.vhModelo_idvhModelo= vhModelo.idvhModelo
                JOIN vhMarca on vhMarca.idvhMarca= vhModelo.vhMarca_idvhMarca
                JOIN seguro on seguro.idseguro= traza.seguro_idseguro'
            );

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
        $bodyOut = [''];

        $bodyIn = $request->getParsedBody();

        if (@$bodyIn['seguimiento']
            && @$bodyIn['seguimiento']['modelo']
            && @$bodyIn['seguimiento']['patente']
            && @$bodyIn['seguimiento']['seguro']
            && @$bodyIn['seguimiento']['orden']
            && @$bodyIn['seguimiento']['fechaIngreso']
            && @$bodyIn['seguimiento']['fechaSalidaAprox']
            && @$bodyIn['seguimiento']['observaciones']
            && @$bodyIn['seguimiento']['idUsuario']
            && @$bodyIn['seguimiento']['piezas']
        ) {

            // $nombre = $filtro->stringFilter($bodyIn['modelo']['modeloNombre']);
            // $marca = $filtro->stringFilter($bodyIn['modelo']['modeloMarca']);
            // $tipo = $filtro->stringFilter($bodyIn['modelo']['modeloTipo']);
            //
            //trazaTipo
            $orden = $bodyIn['seguimiento']['orden'];
            $seguro = $bodyIn['seguimiento']['seguro'];
            $modelo = $bodyIn['seguimiento']['modelo'];
            $patente = $bodyIn['seguimiento']['patente'];
            $fecha_entrega = $bodyIn['seguimiento']['fechaSalidaAprox'];

            $idUsuario = $bodyIn['seguimiento']['idUsuario'];
            $fechaIngreso = $bodyIn['seguimiento']['fechaIngreso'];
            $piezas = $bodyIn['seguimiento']['piezas'];

            ###################
            // HAY QUE MODIFICAR LA BASE DE DATOS SI O SI
            ####################

            /**
             * insertar primero la orden (y guardar el valor)
             * luego la traza (con la orden)
             * luego movimiento (id de usuario, viene en req)
             * ​INSERT INTO `movimiento`
             * (`idmovimiento`, `traza_idtraza`, `usuario_idusuario`, `chSector_idchSector_destino`, `movimiento_fecha`, `movimiento_hora`) VALUES
             * (NULL, '3', '1', '1', '2019-12-10', '11:30:00');
             *
             */

            $mysql = new mysql;

            if ($mysql->conectar()) {
                if (empty($mysql->listar("orden WHERE idorden = $orden"))) {

                    $mysql->insertar("orden", "'$orden'");
                    $mysql->insertar("traza", "NULL, '$seguro', '$orden', '$modelo', '$patente', '$fecha_entrega'");
                    $lastTrazaId = $mysql->getLastId();
                    $mysql->insertar("movimiento", "NULL, '$lastTrazaId', '$idUsuario', '1', '$fechaIngreso', '11:30:00'");

                    foreach ($piezas as $key => $value) {

                        $pieza_nombre = $value['pieza'];
                        $pieza_accion = $value['accion'];

                        $mysql->insertar("pieza", "NULL, '$lastTrazaId', '$pieza_nombre', '$pieza_accion'");
                    }
                }
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

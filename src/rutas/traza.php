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
                'idtraza,orden_idorden,traza_patente,vhModelo,vhMarca,seguro, traza_repuestos',
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

$app->get('/traza/{cat}', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request, $args) {

        $filtro = new filtro;
        $bodyOut = [];

        if ($params = $request->getQueryParams()) {
            if ($params['idtraza'] && $args['cat']) {

                try {
                    $mysql = new mysql;
                    $mysql->conectar();
                    // Obtenemos el ID de la traza
                    $idTraza = $filtro->stringFilter($params['idtraza']);
                    $cat = $args['cat'];

                    switch ($cat) {

                        case 'piezas':
                            $bodyOut = $mysql->listar("pieza WHERE traza_idtraza = $idTraza");
                            return $bodyOut;
                            break;

                        case 'datos':
                            $bodyOut = $mysql->listarCols(
                                "idtraza,orden_idorden,traza_patente,vhModelo,vhMarca,seguro, traza_repuestos",
                                "traza
                                JOIN vhModelo on traza.vhModelo_idvhModelo= vhModelo.idvhModelo
                                JOIN vhMarca on vhMarca.idvhMarca= vhModelo.vhMarca_idvhMarca
                                JOIN seguro on seguro.idseguro= traza.seguro_idseguro
                                WHERE traza.idtraza = $idTraza"
                            );

                            //$bodyOut = $mysql->listar("pieza WHERE traza_idtraza = $idTraza");

                            return $bodyOut;
                            break;

                        case 'mov':
                            $bodyOut = $mysql->listarCols(
                                "usuario.usuario_nombre, usuario.usuario_apellido, chSector.chSector, movimiento_fecha, movimiento_hora ",
                                "movimiento
                                JOIN chSector on movimiento.chSector_idchSector_destino = chSector.idchSector
                                JOIN usuario ON movimiento.usuario_idusuario = usuario.idusuario
                                WHERE movimiento.traza_idtraza = $idTraza"
                            );
                            return $bodyOut;
                            break;

                        default:
                            $bodyOut = ['def'];

                            break;
                    }
                    return $bodyOut;
                } catch (\Throwable $th) {
                    return false;
                }

            }
        }

    };
    return $peticiones->conTokenGet($func($request, $args), true, null);
});

/**
 * POST
 */
$app->post('/traza', function (Request $request, Response $response, array $args) {

    $peticiones = new peticion($request, $response, $args);

    $func = function ($request) {
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
            && @$bodyIn['seguimiento']['esperandoRepuestos']
        ) {

            $orden = intval($bodyIn['seguimiento']['orden']);
            $seguro = intval($bodyIn['seguimiento']['seguro']);
            $modelo = intval($bodyIn['seguimiento']['modelo']);
            $esperandoRep = intval($bodyIn['seguimiento']['esperandoRepuestos']);
            $patente = $filtro->stringFilter($bodyIn['seguimiento']['patente']);
            $fecha_entrega = $filtro->stringFilter($bodyIn['seguimiento']['fechaSalidaAprox']);

            $idUsuario = intval($bodyIn['seguimiento']['idUsuario']);
            $fechaIngreso = $filtro->stringFilter($bodyIn['seguimiento']['fechaIngreso']);

            $piezas = $bodyIn['seguimiento']['piezas'];

            $mysql = new mysql;

            if ($mysql->conectar()) {
                if (empty($mysql->listar("orden WHERE idorden = $orden"))) {

                    /** Inserta la Orden */
                    $mysql->insertar("orden", "'$orden'");

                    /** Inserta la traza */
                    $mysql->insertar("traza", "NULL, '$seguro', '$orden', '$modelo', '$patente', '$fecha_entrega', '$esperandoRep'");
                    $lastTrazaId = $mysql->getLastId();

                    /** Inserta el Movimiento */
                    $mysql->insertar("movimiento", "NULL, '$lastTrazaId', '$idUsuario', '1', '$fechaIngreso', '11:30:00'");

                    /** Inserta las piezas */
                    foreach ($piezas as $key => $value) {

                        $pieza_nombre = $filtro->stringFilter($value['pieza']);
                        $pieza_accion = $filtro->stringFilter($value['accion']);
                        $mysql->insertar("pieza", "NULL, '$lastTrazaId', '$pieza_nombre', '$pieza_accion'");
                    }

                }
                $bodyOut = $bodyIn;

            } else {
                return false;
            }
        }

        return $bodyOut;
    };
    return $peticiones->conTokenPost($func($request), true, null);
});

$app->put('/traza/avanzar', function (Request $request, Response $response, array $args) {$peticiones = new peticion($request, $response, $args);

    $func = function ($request, $args) {

        $filtro = new filtro;
        $bodyOut = [];

        if ($params = $request->getQueryParams()) {
            if ($params['idtraza'] && $params['idusuario']) {
                try {
                    $idtraza = $params['idtraza'];
                    $idusuario = $params['idusuario'];
                    $mysql = new mysql;
                    if ($mysql->conectar()) {
                        $aux = $mysql->listar("movimiento
                                                WHERE traza_idtraza = $idtraza
                                                ORDER BY idmovimiento DESC
                                                LIMIT 1");
                        $ultimoSector = (int) $aux[0]['chSector_idchSector_destino'];
                        $nuevoSector = $ultimoSector + 1;

                        $fecha = date('Y-m-d'); // "2019-12-01",
                        $hora = date('H:i:s'); // '11:30:00'

                        /** Inserta el Movimiento */
                        if ($mysql->insertar("movimiento", "NULL, '$idtraza', '$idusuario', '$nuevoSector', '$fecha', '$hora'")) {
                            $bodyOut = $mysql->listarCols(
                                "movimiento_fecha, movimiento_hora, chSector",
                                "movimiento
                                JOIN chSector ON movimiento.chSector_idchSector_destino = chSector.idchSector
                                WHERE movimiento.traza_idtraza = $idtraza ORDER BY movimiento.idmovimiento DESC LIMIT 1"
                            );
                            return $bodyOut;
                        }
                    }
                } catch (\Throwable $th) {
                    $bodyOut['err'] = $th;
                    return false;
                }

            }
        }

    };
    return $peticiones->conTokenGet($func($request, $args), true, null);

});
// SELECT *
// FROM `movimiento`
// WHERE traza_idtraza = 23
// ORDER BY idmovimiento DESC
// LIMIT 1
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

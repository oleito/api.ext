<?php
/*
 * GET    Obtiene todos los elementos de la entidad Usuario
 * GET    Obtiene el elemento con Id 1 de la entidad Usuario

 * POST Publica un nuevo elemento de la entidad Usuario
 * PUT Modifica el elemento con Id 1 de la entidad Usuario
 * DELETE Elimina el elemento con Id 1 de la entidad Usuario
 */
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/subirbase', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request) {

        $lng = 0;
        /* RECIBIMOS LA BASE */
        $rq = $request->getParsedBody();
        //$rq = json_decode($rq);
        $rp = [];

        $lng = count($rq);
        /* si viene algo en el array */

        //if (false) {
        if ($lng > 0) {
            //array_push($rp, "entra al IF");

            $db = new db();
            $db = $db->conectar();

            for ($i = 0; $i < $lng; $i++) {
                $filaRp = [];

                $fila = $rq[$i];

                /* SEPARAMOS LOS DATOS SEGUN SU CATEGORIA */
                $cliente = [];
                $vehiculo = [];
                $orden = [];

                /*************** CLIENTE ***************/
                $cliente = [
                    'clienteNumero' => intval($fila['clienteNumero']),
                    'clienteApellido' => $fila['clienteApellido'],
                    'clienteNombre' => $fila['clienteNombre'],
                    'clienteCorreo' => $fila['clienteCorreo'],
                    'clienteTelefono' => $fila['clienteTelefono'],
                ];
                $filaRp = array_merge($filaRp, $cliente);

                $consulta = "SELECT * FROM cliente WHERE idcliente=" . $cliente['clienteNumero'] . " AND cliente_activo='1' LIMIT 1";

                $ejecutar = $db->query($consulta);
                $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($resp[0])) {
                    //si ya existe el cliente
                } else {
                    $data = [
                        'cliente' => $cliente['clienteNumero'],
                        'nombre' => $cliente['clienteNombre'],
                        'apellido' => $cliente['clienteApellido'],
                        'telefono' => $cliente['clienteTelefono'],
                    ];
                    $sql = "INSERT INTO cliente VALUES (:cliente, :nombre, :apellido,:telefono, 1)";

                    $stmt = $db->prepare($sql);
                    $stmt->execute($data);
                    //array_push($rp, "cliente cargado ");
                }

                /*************** CORREO ***************/
                if (!empty($fila['clienteCorreo'])) {

                    $consulta = "SELECT * FROM correo WHERE correo_dir='" . $cliente['clienteCorreo'] . "' AND correo_activo='1' LIMIT 1";

                    $ejecutar = $db->query($consulta);
                    $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($resp[0])) {
                        //el cliente ya existe
                    } else {
                        $data = [
                            'cliente' => $cliente['clienteNumero'],
                            'correo' => $cliente['clienteCorreo'],
                        ];
                        $sql = "INSERT INTO correo VALUES (null, :correo, 1, :cliente)";

                        $stmt = $db->prepare($sql);
                        $stmt->execute($data);
                    }
                } else {
                    //array_push($rp, "no viene el correo");
                }

                /*************** VEHICULO ***************/
                $vehiculo = [
                    'vehiculoVin' => $fila['vehiculoVin'],
                    'vehiculoDominio' => $fila['vehiculoDominio'],
                    'vehiculoMarca' => intval($fila['vehiculoMarca']),
                    'vehiculoModelo' => $fila['vehiculoModelo'],
                    'vehiculoColor' => $fila['vehiculoColor'],
                    'vehiculoFinGarantia' => $fila['vehiculoFinGarantia'],
                ];
                $filaRp = array_merge($filaRp, $vehiculo);

                $consulta = "SELECT * FROM vehiculo WHERE vehiculo_vin='" . $vehiculo['vehiculoVin'] . "' AND vehiculo_active='1' LIMIT 1";

                $ejecutar = $db->query($consulta);
                $vh = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($vh[0])) {
                    //si ya existe el cliente
                } else {
                    $fecha = "0-0-0";
                    if (!empty($vehiculo['vehiculoFinGarantia'])) {
                        $fecha = parsearFecha($vehiculo['vehiculoFinGarantia']);
                    }
                    $data = [
                        'vin' => $vehiculo['vehiculoVin'],
                        'patente' => $vehiculo['vehiculoDominio'],
                        'marca' => $vehiculo['vehiculoMarca'],
                        'modelo' => $vehiculo['vehiculoModelo'],
                        'color' => $vehiculo['vehiculoColor'],
                        'fgtia' => $fecha,
                        'cliente' => $cliente['clienteNumero'],
                    ];
                    $sql = "INSERT INTO vehiculo VALUES (:vin, :patente, :marca, :modelo, :color, :fgtia, :cliente, 1)";
                    //array_push($rp, $sql);

                    $stmt = $db->prepare($sql);
                    $stmt->execute($data);

                }

                /*************** ORDEN ***************/
                /* NOS FIJAMOS SI EXISTE LA ORDEN, SINO, LA CREAMOS*/
                $rc = explode("PV", $fila['recepcionista']);
                if (!empty($rc[1])) {
                    $rc = intval(substr($rc[1], -2));
                } else {
                    $rc = 0;
                }
                $orden = [
                    'ordenNumero' => intval($fila['ordenNumero']),
                    'ordenFecha' =>   $fila['ordenFecha'],
                    'recepcionista' => $rc,
                ];
                $filaRp = array_merge($filaRp, $orden);

                $consulta = "SELECT * FROM orden WHERE idorden=" . $orden['ordenNumero'] . " AND orden_activo='1' LIMIT 1";

                $ejecutar = $db->query($consulta);
                $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

                /**/
                if (!empty($resp[0])) {
                    //si ya existe el cliente
                    //$cliente = $resp[0];
                } else {
                    $data = [
                        'orden' => $orden['ordenNumero'],
                        'fecha' => parsearFecha($orden['ordenFecha']),
                        'vin' => $vehiculo['vehiculoVin'],
                        'rc' => $orden['recepcionista']
                    ];
                    $sql = "INSERT INTO orden VALUES ( :orden, :fecha, :vin, 0, 1, :rc)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute($data);
                }
                array_push($rp, $filaRp);
            }
        }

        //array_push($rq, $respuesta);
        return $rp;
    };

    return $peticiones->getRequest($func($request));
});

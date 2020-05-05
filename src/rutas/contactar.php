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

/****** LISTADO COMPLETO ******/
$app->get('/contactar', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request) {

        $db = new db();
        $db = $db->conectar();

        $consulta = "
            SELECT
            orden.idorden,
            orden.orden_fecha,
            orden.orden_estado,
            cliente.cliente_nombre,
            cliente.cliente_apellido,
            cliente.cliente_telefono,
            vehiculo.vehiculo_marca,
            vehiculo.vehiculo_modelo,
            vehiculo.vehiculo_color
            FROM orden
            JOIN vehiculo ON vehiculo.vehiculo_vin = orden.vehiculo_vinvehiculo
            JOIN cliente on cliente.idcliente = vehiculo.cliente_idcliente
            WHERE  orden_activo='1'
        ";

        $ejecutar = $db->query($consulta);
        $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($resp)) {

            $resp = array_chunk($resp, 15);
            $resp = $resp[0];

            return $resp;
        }
        return null;
    };

    return $peticiones->getRequest($func($request));
});
#.get('/contactar')

/****** DETALLE DE LA ORDEN ******/
$app->get('/contactar/{orden}', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request, $args) {

        $orden = $args['orden'];
        $db = new db();
        $db = $db->conectar();

        $consulta = "
            SELECT
            orden.idorden,
            orden.orden_fecha,
            orden.orden_estado,
            rc.rc_nombre,
            cliente.idcliente as cliente_id,
            cliente.cliente_nombre,
            cliente.cliente_apellido,
            cliente.cliente_telefono,
            vehiculo.vehiculo_marca,
            vehiculo.vehiculo_modelo,
            vehiculo.vehiculo_patente,
            vehiculo.vehiculo_vin
            FROM orden
            JOIN vehiculo ON vehiculo.vehiculo_vin = orden.vehiculo_vinvehiculo
            JOIN cliente on cliente.idcliente = vehiculo.cliente_idcliente
            JOIN rc on rc.id_rc = orden.rc_id_rc
            WHERE  orden_activo='1' AND orden.idorden='$orden'
        ";

        $ejecutar = $db->query($consulta);
        $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);
        $cliente = $resp[0]["cliente_id"];

        $consulta = "
            SELECT correo.correo_dir as cliente_correo
            FROM correo
            WHERE cliente_idcliente = '$cliente'
            ORDER BY idcorreo ASC LIMIT 1
        ";

        $ejecutar = $db->query($consulta);
        $correo = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

        if (isset($correo[0]["cliente_correo"])) {

            $resp[0]['cliente_correo'] = $correo[0]["cliente_correo"];
        } else {
            $resp[0]['cliente_correo'] = "N-A";
        }

        if (!empty($resp)) {
            return $resp[0];
        }
        return null;
    };

    return $peticiones->getRequest($func($request, $args));
});
#.get('/contactar/{orden}')

/****** CARGA DEL CONTACTO ******/
$app->post('/contactar/{orden}', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request, $args) {

        $rq = $request->getParsedBody();
        $rq["orden"] = $args['orden'];

        $lng = count($rq);

        $filaRp = [];

        #2019-04-01...
        $fecha = date("Y-m-d");
        #14:38:25...
        $hora = date("H:i:s");

        $db = new db();
        $db = $db->conectar();

        if ($rq['contactado']) {
            // if (1 != 2) {
            # si fue contactado, carga la recomendacion...
            $data = [
                'orden' => $rq['orden'],
                'nota' => $rq['nota'],
                'fecha' => $fecha,
                'hora' => $hora,
            ];
            $filaRp = array_merge($filaRp, $data);

            $sql = "INSERT INTO contacto VALUES (null, :fecha, :hora, :nota, null, :orden)";

            $stmt = $db->prepare($sql);
            $stmt->execute($data);

            # Obtener el ID al insertar un contacto, necesario para las llaves foraneas
            $idContacto = $db->lastInsertId();

            if ($rq['reclamo']) {
                //if (1==1) {
                # Si existe reclamo
                $data = [
                    'idContacto' => $idContacto,
                    'reclamoMotivo' => $rq['reclamoMotivo'],
                    'reclamoDetalle' => $rq['reclamoDetalle'],
                ];
                $filaRp = array_merge($filaRp, $data);

                $sql = "INSERT INTO reclamo VALUES (null, :reclamoMotivo, :reclamoDetalle, :idContacto)";

                $stmt = $db->prepare($sql);
                $stmt->execute($data);
            }
            if ($rq['retorno']) {
                # si existe retorno
                $data = [
                    'idContacto' => $idContacto,
                    'retornoMotivo' => $rq['retornoMotivo'],
                    'retornoDetalle' => $rq['retornoDetalle'],
                ];
                $filaRp = array_merge($filaRp, $data);
                $sql = "INSERT INTO retorno VALUES (null, :retornoMotivo, :retornoDetalle, :idContacto)";

                $stmt = $db->prepare($sql);
                $stmt->execute($data);
            }
        } else {
            # si no fue contactado, carga el motivo...
            $data = [
                'orden' => $rq['orden'],
                'motivo' => $rq['contactadoMotivo'],
                'fecha' => $fecha,
                'hora' => $hora,
            ];
            $filaRp = array_merge($filaRp, $data);

            $sql = "INSERT INTO contacto VALUES (null, :fecha, :hora, null, :motivo, :orden)";

            $stmt = $db->prepare($sql);
            $stmt->execute($data);
        }

        return $filaRp;

    };

    return $peticiones->getRequest($func($request, $args));
});
#.post('/contactar/{orden}')

/****** ACTUALIZA EL CORREO ******/
$app->put('/contactar/{orden}', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request, $args) {

        $rq = $request->getParsedBody();
        $rq["orden"] = $args['orden'];

        $lng = count($rq);

        $filaRp = [];

        #2019-04-01...
        $fecha = date("Y-m-d");
        #14:38:25...
        $hora = date("H:i:s");

        $db = new db();
        $db = $db->conectar();

        if ($rq['contactado']) {
            // if (1 != 2) {
            # si fue contactado, carga la recomendacion...
            $data = [
                'orden' => $rq['orden'],
                'nota' => $rq['nota'],
                'fecha' => $fecha,
                'hora' => $hora,
            ];
            $filaRp = array_merge($filaRp, $data);

            $sql = "INSERT INTO contacto VALUES (null, :fecha, :hora, :nota, null, :orden)";

            $stmt = $db->prepare($sql);
            $stmt->execute($data);

            # Obtener el ID al insertar un contacto, necesario para las llaves foraneas
            $idContacto = $db->lastInsertId();

        }
        return $filaRp;

    };

    return $peticiones->getRequest($func($request, $args));
});
#.put('/contactar/{orden}')

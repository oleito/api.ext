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
$app->get('/kpioperarios', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request) {
        $filaRp = [];
        $fecha = date("Y-m-d");

        $db = new db();
        $db = $db->conectar();

        $consulta = "SELECT operario_idOperario, fecha FROM kpioperarios ORDER BY kpioperarios.fecha DESC LIMIT 1 ";

        $ejecutar = $db->query($consulta);
        $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($resp[0])) {

            $ultimaFecha = $resp[0]['fecha'];

            $sql = "SELECT * FROM kpioperarios WHERE fecha = ''";
            $sql = "SELECT * FROM kpioperarios JOIN operario on operario.idoperario=kpioperarios.operario_idoperario WHERE fecha = '" . $ultimaFecha . "'";

            $ejecutar = $db->query($sql);
            $opDatos = $ejecutar->fetchAll(PDO::FETCH_ASSOC);
            //array_push($filaRp, $resp[0]);
        }

        $sql = "SELECT fecha FROM kpioperarios WHERE kpioperarios.retoques != 0 ORDER BY fecha DESC LIMIT 1";

        $ejecutar = $db->query($sql);
        $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);
        $ultimaFecha = $resp[0]['fecha'];

        $fecha1 = new DateTime($fecha);
        $fecha2 = new DateTime($ultimaFecha);
        $diff = $fecha1->diff($fecha2);

        $respuesta['dias'] = $diff->days;

        $respuesta['operarios'] = $opDatos;
        // $respuesta['dias'] = $intervalo->format('%R%a');

        return $respuesta;
    };

    return $peticiones->getRequestSinToken($func($request));
});
#.get('/contactar')

/****** CARGA DE DATOS DEL KPI ******/
$app->post('/kpioperarios', function (Request $request, Response $response, array $args) {

    $peticiones = new peticionGet($request, $response);

    $func = function ($request, $args) {

        $rq = $request->getParsedBody();
        $operarios = count($rq);

        if ($operarios >= 1) {
            $filaRp = [];
            $data = [];
            #2019-04-01...
            $fecha = date("Y-m-d");

            $db = new db();
            $db = $db->conectar();

            $consulta = "SELECT operario_idOperario, fecha FROM kpioperarios ORDER BY kpioperarios.fecha DESC LIMIT 1 ";

            $ejecutar = $db->query($consulta);
            $resp = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($resp[0])) {

                $sql = "ultima fecha disponible";
                array_push($filaRp, $sql);
                array_push($filaRp, $resp[0]['fecha']);

                $ultimaFecha = $resp[0]['fecha'];

                /**************/
                for ($i = 0; $i < $operarios; $i++) {
                    $fila = $rq[$i];

                    $sql = "SELECT * FROM kpioperarios WHERE operario_idOperario= $i AND fecha = '" . $fecha . "'";

                    $ejecutar = $db->query($sql);
                    $existentes = $ejecutar->fetchAll(PDO::FETCH_ASSOC);
                    //array_push($filaRp, $sql);

                    if (!empty($existentes[0])) {
                        #update regitro
                        $sql = "Registro ya existe con fecha";
                        //array_push($filaRp, $sql);
                        $sql = "UPDATE kpioperarios SET porcentaje_carga = '" . $fila['porcentaje_carga'] . "', horas_facturadas = '" . $fila['horas_facturadas'] . "', retoques = '" . $fila['retoques'] . "', auditoria = '" . $fila['auditoria'] . "' WHERE kpioperarios.idkpioperarios = " . $existentes[0]['idkpioperarios'] . "; ";

                        $stmt = $db->prepare($sql);
                        $stmt->execute($data);

                    } else {
                        #crear registro
                        $data = [
                            'porcentaje_carga' => intval($fila['porcentaje_carga']),
                            'horas_facturadas' => intval($fila['horas_facturadas']),
                            'retoques' => intval($fila['retoques']),
                            'auditoria' => intval($fila['auditoria']),
                            'idOperario' => intval($fila['idOperario']),
                            'fecha' => $fecha,
                        ];

                        $sql = "INSERT INTO kpioperarios VALUES (NULL, :porcentaje_carga, :horas_facturadas, :retoques, :auditoria, :idOperario, :fecha)";

                        $stmt = $db->prepare($sql);
                        $stmt->execute($data);
                        //array_push($filaRp, $sql);
                    }
                } //for ($i = 0; $i < $operarios; $i++)
                /************/
            } //if (!empty($resp[0]))
            return $filaRp;
        } //if ($operarios >= 1)
    };

    return $peticiones->getRequest($func($request, $args));
});
#.post('/contactar/{orden}')

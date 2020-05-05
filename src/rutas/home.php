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

$app->get('/home', function (Request $request, Response $response, array $args) {
    $peticiones = new peticionGet($request, $response);

    $func = function () {
        $db = new db();
        $db = $db->conectar();
        $consulta = "SELECT * FROM mensaje";
        $ejecutar = $db->query($consulta);

        $mensaje = $ejecutar->fetchAll(PDO::FETCH_ASSOC);

        return 'contenido para el body.';
    };

    return $peticiones->getRequest($func());
});

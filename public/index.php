<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/classes/token.php';
require '../src/classes/peticion.php';
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

/*
$config['db']['host']   = 'localhost';
$config['db']['user']   = 'user';
$config['db']['pass']   = 'password';
$config['db']['dbname'] = 'exampleapp';
(['settings' => $config])
 */

$app = new \Slim\App;

################### RAIZ ###########################
/**
 * GET
 */
$app->get('/', function (Request $request, Response $response, array $args) {
    $body = [];

    $peticiones = new peticion($request, $response);

    $func = function ($request) {

        $resp = array('respuesta' => "soy una repuesta");

        return $resp;
    };

    return $peticiones->conToken($func($request));

});

/**
 * POST
 */
$app->post('/', function (Request $request, Response $response, array $args) {
    $body = $request->getParsedBody();
    if ($request->hasHeader('PHP_AUTH_USER') || $request->hasHeader('PHP_AUTH_PW')) {
        //$body = $request->getHeaders();
        $body["HTTP_AUTHORIZATION"] = $request->getHeader("HTTP_AUTHORIZATION")[0]; //Basic
        $body["explode"] = base64_decode(explode(" ", $body["HTTP_AUTHORIZATION"])[1]);
        $body["Datos"] = array(
            'usuario' => explode(":", $body["explode"])[0],
            'pass' => explode(":", $body["explode"])[1],
        );
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200)
        ->withJson($body);
});

/**
 * PUT
 */
$app->put('/', function (Request $request, Response $response, array $args) {

});

/**
 * DELETE
 */
$app->delete('/', function (Request $request, Response $response, array $args) {

});
########################## END ##########################

/**
 *  EJEMPLO
 */
$app->post('/hola/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hola, $name");
    return $response;
});
/**
 * ONLY FOR TESTING USE
 */
$app->get('/comercios', function (Request $request, Response $response, array $args) {
    $body = array(
        'token' => 12345,
        'body' => array(
            array('Nombre' => 'sandrita', 'telefono' => '266 1234567'),
            array('Nombre' => 'sandrita', 'telefono' => '266 1234567'),
            array('Nombre' => 'sandrita', 'telefono' => '266 1234567'),
            array('Nombre' => 'sandrita', 'telefono' => '266 1234567'),
        ),
    );
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200)
        ->withJson($body);
});

/** clase con funciones utiles */
class utilidad
{
    public function __contrucc()
    {
    }
    public function invertir($a)
    {
        $a = explode(".", $a);
        $b = $a[1] . "." . $a[0];
        return $b;
    }
    public function enviarCorreo($datos)
    {
        $name = $datos['nombre'];
        $email = $datos['email'];
        $titulo = 'Mensaje desde la web - ParisAutos';
        $mensaje = '<html><body>';
        $mensaje = 'Turno solicitado desde la web';
        $mensaje .= '<br> Nombre: ' . $name . "\r\n";
        $mensaje .= '<br> Email: ' . $email . "\r\n";
        $mensaje .= '</body></html>';
        $cabeceras = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $cabeceras .= 'From: Sistemas <sistemas@parisautos.com.ar>' . "\r\n";
        $cabeceras .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
        mail("sistemas@parisautos.com.ar", $titulo, $mensaje, $cabeceras);
        return $mensaje;
    }
    public function setSector($sector)
    {
        switch ($sector) {
            case 'postventa':
                $para = 'recepcion.postventa@parisautos.com.ar';
                break;
            case 'administracionplanes': //planes
                $para = 'diego.moreno@parisautos.com.ar';
                break;
            case 'administracionusados': //planes
                $para = 'recepcion@parisautos.com.ar';
                break;
            case 'usados':
                $para = 'mauro.soratto@parisautos.com.ar';
                break;
            case 'autoplancitroen':
                $para = 'diego.moreno@parisautos.com.ar';
                break;
            case 'autoplanpeugeot':
                $para = 'diego.moreno@parisautos.com.ar';
                break;
            case '0kmcitroen':
                $para = 'mauro.soratto@parisautos.com.ar';
                break;
            case '0kmpeugeot':
                $para = 'mauro.soratto@parisautos.com.ar';
                break;
            default:
                $para = 'info@parisautos.com.ar';
                break;
        }
        return $para;
    }
}
$app->run();

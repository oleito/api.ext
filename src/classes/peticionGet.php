<?php

class peticionGet
{
    private $request = null;
    private $response = null;

    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest($anonima)
    {
        //declaracion de variables.
        $tokenIn = null;
        $tokenOut = null;

        $mensajesPendientes = null;

        $body = null;

        $tokenIn = $this->request->getHeaderLine('token');

        if ($tokenIn != "" && $tokenIn) {
            $token = new token();
            if ($token->checkToken($tokenIn)) {
                //una vez aprobado el token, podemos seguir operando.
                //trata de hacer la consulta correspondiente
                //en caso de error, devuelve el informe correspo

                try {
                    /** Asigna al $body el contenido de la funcion anonima**/
                    $body = $anonima;

                    /* Busca nuevos mensajes pendientes. */
                    //$mensajesPendientes = new mensajesPendientes();
                    //$mensajesPendientes = $mensajesPendientes->getMensajesPendientes();

                    $msgs = array();

                    array_push($msgs, ['from' => 'lean', 'msg' => "chau"]);
                    array_push($msgs, ['from' => 'x', 'msg' => "d"]);

                    $tokenOut = $token->updateToken($tokenIn);

                    $resp = array(
                        'token' => $tokenOut,
                        #'msjs' => $msgs,
                        #'msjQ' => count($msgs),
                        'body' => $body,
                    );

                    //$resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    $resp = json_encode($resp);
                    return $this->response
                        ->withStatus(200)
                        ->write($resp);
                    //->write($resp);
                } catch (PDOException $e) {
                    $resp = array(
                        'token' => null,
                        'body' => print_r($e->getMessage()),
                    );

                    $resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    return $this->response
                        ->withStatus(500)
                        ->write($resp);
                }
            } else {
                $resp = array(
                    'token' => null,
                    'body' => null,
                );

                $resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

                return $this->response
                    ->withStatus(401)
                    ->write(null);
            }
        } else {
            $resp = array(
                'token' => null,
                'body' => null,
            );

            $resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            return $this->response
                ->withStatus(402)
                ->write(null);
        }
    }

    public function getRequestSinToken($anonima)
    {
        //declaracion de variables.

        try {
            /** Asigna al $body el contenido de la funcion anonima**/
            $body = $anonima;

            /* Busca nuevos mensajes pendientes. */
            //$mensajesPendientes = new mensajesPendientes();
            //$mensajesPendientes = $mensajesPendientes->getMensajesPendientes();

            $msgs = array();

            array_push($msgs, ['from' => 'lean', 'msg' => "chau"]);
            array_push($msgs, ['from' => 'x', 'msg' => "d"]);


            $resp = array(
                'token' => 'SinToken',
                #'msjs' => $msgs,
                #'msjQ' => count($msgs),
                'body' => $body,
            );

            //$resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $resp = json_encode($resp);
            return $this->response
                ->withStatus(200)
                ->write($resp);
            //->write($resp);
        } catch (PDOException $e) {
            $resp = array(
                'token' => null,
                'body' => print_r($e->getMessage()),
            );

            $resp = json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            return $this->response
                ->withStatus(500)
                ->write($resp);
        }

    }
}

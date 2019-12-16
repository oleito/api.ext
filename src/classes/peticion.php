<?php
class peticion
{
    private $request = null;
    private $response = null;
    private $args = null;

    public function __construct($request, $response, $args)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
    }

    public function conTokenGet($funcionAnonina)
    {
        $token = new token;
        if ($this->request->hasHeader('HTTP_TOKEN') && $token->checkToken($this->request->getHeader("HTTP_TOKEN")[0])) {
            $body["HTTP_TOKEN"] = $this->request->getHeader("HTTP_TOKEN")[0];

            $body = array(
                'token' => $token->updateToken($this->request->getHeader("HTTP_TOKEN")[0]),
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200)
                ->withJson($body);
        } else {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401);
        }
    }

    public function conTokenPost($funcionAnonina, $status, $code)
    {
        $token = new token;
        if (!$status) {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus($code);
        } else if ($this->request->hasHeader('HTTP_TOKEN') && $token->checkToken($this->request->getHeader("HTTP_TOKEN")[0])) {
            $body["HTTP_TOKEN"] = $this->request->getHeader("HTTP_TOKEN")[0];

            $body = array(
                'token' => $token->updateToken($this->request->getHeader("HTTP_TOKEN")[0]),
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201)
                ->withJson($body);
        } else {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401);
        }
    }

    public function conTokenPut($funcionAnonina)
    {
        $token = new token;
        if ($this->request->hasHeader('HTTP_TOKEN') && $token->checkToken($this->request->getHeader("HTTP_TOKEN")[0])) {
            $body["HTTP_TOKEN"] = $this->request->getHeader("HTTP_TOKEN")[0];

            $body = array(
                'token' => $token->updateToken($this->request->getHeader("HTTP_TOKEN")[0]),
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200)
                ->withJson($body);
        } else {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401);
        }
    }

    public function conTokenDelete($funcionAnonina)
    {
        $token = new token;
        if ($this->request->hasHeader('HTTP_TOKEN') && $token->checkToken($this->request->getHeader("HTTP_TOKEN")[0])) {
            $body["HTTP_TOKEN"] = $this->request->getHeader("HTTP_TOKEN")[0];

            $body = array(
                'token' => $token->updateToken($this->request->getHeader("HTTP_TOKEN")[0]),
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200)
                ->withJson($body);
        } else {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(401);
        }
    }

    /* *************************************************** */
    public function sinTokenPost($funcionAnonina, $status, $code)
    {
        if (!$status) {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus($code);
        } else {
            if ($this->request->hasHeader('HTTP_TOKEN')) {
                $body["HTTP_TOKEN"] = $this->request->getHeader("HTTP_TOKEN")[0];
            }

            $body = array(
                'token' => null,
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201)
                ->withJson($body);
        }
    }

    public function sinTokenLogin($funcionAnonina, $status, $code)
    {
        $token = new token;
        if (!$status) {
            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus($code);
        } else {

            #### datos de ejemplo
            $usuario = array(
                'id' => 036,
                'nombre' => 'Leandro',
                'access' => [
                    01,
                    02,
                    07,
                ],
            );
            ##############

            $body = array(
                'token' => $token->setToken($usuario),
                'body' => $funcionAnonina,
            );

            return $this->response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(201)
                ->withJson($body);
        }
    }

}

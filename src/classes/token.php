<?php
/**
 *
 */
date_default_timezone_set('America/Argentina/San_Luis');

class token
{
    //Declaracion de variables
    private $secret = 'postventa2019';
    private $timeOut = 1000;
    private $expire = null;
    /*
     */
    public function __construct()
    {
        $now = strtotime('now');
        $this->expire = $now + ($this->timeOut * 60);
    }
    private function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    /**
     * Recibe un arreglo y devuelve un token que contiene los datos del usuario
     */
    public function setToken($usuario)
    {
        //return "Token de prueba";
        try {
            //Define los Headers del token
            $header = self::base64url_encode(json_encode([
                "alg" => "HS256",
                "typ" => "JWT",
            ]));
            //Define el payload y carga los datos del usuario
            $payload = self::base64url_encode(json_encode([
                "expire" => $this->expire, //esta linea dejarla asi, xq sino rompe
                "user" => $usuario,
            ]));
            $signature = self::base64url_encode(hash_hmac("sha256", $header . "." . $payload, $this->secret, true));
            //Construye el token = header+paylad+signature
            $token = $header . "." . $payload . "." . $signature;

            return $token;
        } catch (\Throwable $th) {
            return null;
        }
    }
    /**
     * Verifica si el token es Valito
     * @param  [] $token [token que envia el frontend]
     * @return [bool]        [Verdadero si es valido]
     */
    public function checkToken($token)
    {

  //      return true; ################ dev prop

        /**
         * ESTA FUNCION DEBE EVALUAR SI EL TOKEN ES VALIDO
         * TAMBIEN SI EL USUARIO ESTA AUTORIZADO PARA REALIZAR LA CONSULTA
         */

        try {
            //Divide el Token en 3 Partes
            if ($t = explode(".", $token)) {

                //asigna los valores a referencias
                $refHeader = $t[0];
                $refPayload = $t[1];
                $refSignature = $t[2];
                //Crea un nuevo signature con los datos del token
                $newSignature = self::base64url_encode(hash_hmac("sha256", $refHeader . "." . $refPayload, $this->secret, true));
                //decodifica el payload
                $refPayload = json_decode(base64_decode($refPayload), true);
                //obtiene el momento en que expira
                $expire = $refPayload['expire'];
                //Verifica si el token ha expirado
                if ($expire >= strtotime('now') && $refSignature === $newSignature) {
                    // si aun es valido
                    return true;
                }
            }
            return false;

        } catch (\Throwable $th) {
            // Si ya expiro
            return false;
        }
    }
    /**
     * Renueva un token existente
     * @param  [] $token [token que envia el frontend]
     * @return [bool]        [Verdadero si es valido]
     */
    public function updateToken($token)
    {
        //return "Token de prueba";
        try {
            //Divide el Token en 3 Partes
            if ($t = explode(".", $token)) {
                $refHeader = $t[0];
                $refPayload = $t[1];

                //decodifica el payload
                $refPayload = json_decode(base64_decode($refPayload), true);
                //actualiza EXPIRE
                $refPayload['expire'] = $this->expire;
                //codificamos el nuevo Payload con
                $newPayload = self::base64url_encode(json_encode($refPayload));
                //Crea un nuevo signature con los datos del token
                $signature = self::base64url_encode(hash_hmac("sha256", $refHeader . "." . $newPayload, $this->secret, true));
                //Construye el token = header+paylad+signature
                $newToken = $refHeader . "." . $newPayload . "." . $signature;
                return $newToken;
            }
            return false;
        } catch (\Throwable $th) {
            return false;

        }
    }
}

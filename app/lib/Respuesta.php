<?php

namespace App\Lib;

class Respuesta
{
    public static $datos = null;
    public static $correcta = false;
    public static $mensaje = '';

    public static function set($correcta, $mensaje = '', $datos = null)
    {
        self::$correcta = $correcta;
        self::$mensaje = $mensaje;
        self::$datos = $datos == null ? self::$datos : $datos;
    }

    public static function setDatos($datos)
    {
        self::$datos = $datos;
    }

    public static function setCorrecta($correcta)
    {
        self::$correcta = $correcta;
    }

    public static function setMensaje($mensaje)
    {
        self::$mensaje = $mensaje;
    }

    public static function toString()
    {
        return array(
            "datos" => self::$datos,
            "correcta" => self::$correcta,
            "mensaje" => self::$mensaje,
        );
    }
}

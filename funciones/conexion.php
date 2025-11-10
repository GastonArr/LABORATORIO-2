<?php
// Archivo de conexión siguiendo la estructura presentada en clase.
/**
 * Archivo: funciones/conexion.php (línea 10).
 * Propósito: establecer una conexión MySQL que será reutilizada por todo el sistema.
 * Cómo lo hace: invoca mysqli_connect con los parámetros recibidos, verifica que la conexión sea válida
 *              y configura el conjunto de caracteres a utf8 para soportar caracteres especiales.
 * Devuelve: recurso mysqli activo que permite ejecutar consultas; detiene el script si la conexión falla.
 */
function ConexionBD($Host = 'localhost', $User = 'root', $Password = '', $BaseDeDatos = 'tdcsa') {
    $linkConexion = mysqli_connect($Host, $User, $Password, $BaseDeDatos);

    if ($linkConexion === false) {
        die('No se pudo establecer la conexión.');
    }

    mysqli_set_charset($linkConexion, 'utf8');

    return $linkConexion;
}

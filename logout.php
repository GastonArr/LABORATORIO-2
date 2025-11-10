<?php
/**
 * Script encargado de cerrar la sesión actual.
 * Aunque parece muy corto, es fundamental para que un usuario pueda salir de forma segura.
 *
 * Flujo:
 * 1. Incluye las dependencias para reutilizar las funciones de sesión.
 * 2. Llama a CerrarSesionUsuario() que destruye los datos guardados en $_SESSION.
 * 3. Redirecciona inmediatamente al formulario de login.
 *
 * Si este archivo no existiera no habría un punto único para "desloguearse" del panel, por lo que es necesario.
 */

// Dependencia de conexión (no se utiliza directamente aquí, pero garantiza inicializar la sesión y configuración común).
require_once 'funciones/conexion.php';
// Biblioteca con las funciones auxiliares, entre ellas CerrarSesionUsuario y Redireccionar.
require_once 'funciones/funciones.php';

// Elimina todos los datos relacionados con el usuario en la sesión actual.
CerrarSesionUsuario();
// Luego de cerrar la sesión se envía al usuario al formulario de inicio de sesión.
Redireccionar('login.php');

<?php
require_once 'funciones/conexion.php';

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Archivo: funciones/funciones.php (línea 15).
 * Propósito: redirigir inmediatamente a otra ruta del sistema.
 * Cómo lo hace: envía la cabecera HTTP "Location" con la ruta destino y finaliza el script con exit
 *              para evitar que se siga ejecutando código.
 * Devuelve: void porque solo produce el efecto de redireccionar sin retornar valores.
 */
function Redireccionar($Ruta)
{
    header('Location: ' . $Ruta);
    exit;
}

/**
 * Archivo: funciones/funciones.php (línea 28).
 * Propósito: recuperar la información completa del usuario que intenta iniciar sesión.
 * Cómo lo hace: arma una consulta SELECT con el usuario y clave recibidos, ejecuta la consulta y
 *              arma un arreglo asociativo con todos los campos relevantes para la sesión.
 * Devuelve: array con los datos del usuario activo o un array vacío si las credenciales no existen.
 */
function DatosLogin($vUsuario, $vClave, $vConexion)
{
    $Usuario = array();

    $SQL = "SELECT U.id, U.apellido, U.nombre, U.usuario, U.clave, U.id_nivel, U.imagen, U.activo,"
        . " N.denominacion AS NombreNivel"
        . " FROM usuarios U, niveles N"
        . " WHERE U.id_nivel = N.id"
        . " AND U.usuario = '" . $vUsuario . "'"
        . " AND U.clave = '" . $vClave . "'";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $data = mysqli_fetch_array($rs);

        if (!empty($data)) {
            $Usuario['ID'] = $data['id'];
            $Usuario['APELLIDO'] = $data['apellido'];
            $Usuario['NOMBRE'] = $data['nombre'];
            $Usuario['USUARIO'] = $data['usuario'];
            $Usuario['CLAVE'] = $data['clave'];
            $Usuario['NIVEL'] = $data['id_nivel'];
            $Usuario['NIVEL_NOMBRE'] = $data['NombreNivel'];
            $Usuario['ACTIVO'] = $data['activo'];

            if (empty($data['imagen'])) {
                $Usuario['IMG'] = 'user.png';
            } else {
                $Usuario['IMG'] = $data['imagen'];
            }

            $Usuario['SALUDO'] = 'Hola';
        }

        mysqli_free_result($rs);
    }

    return $Usuario;
}

/**
 * Archivo: funciones/funciones.php (línea 76).
 * Propósito: almacenar en variables de sesión los datos del usuario autenticado.
 * Cómo lo hace: toma cada valor recibido en el arreglo $DatosUsuario y lo guarda en $_SESSION,
 *              asignando valores por defecto cuando algún índice está vacío.
 * Devuelve: void porque solamente inicializa la sesión con la información del usuario.
 */
function GuardarSesionUsuario($DatosUsuario)
{
    $_SESSION['Usuario_ID'] = !empty($DatosUsuario['ID']) ? $DatosUsuario['ID'] : 0;
    $_SESSION['Usuario_Nombre'] = !empty($DatosUsuario['NOMBRE']) ? $DatosUsuario['NOMBRE'] : '';
    $_SESSION['Usuario_Apellido'] = !empty($DatosUsuario['APELLIDO']) ? $DatosUsuario['APELLIDO'] : '';
    $_SESSION['Usuario_Usuario'] = !empty($DatosUsuario['USUARIO']) ? $DatosUsuario['USUARIO'] : '';
    $_SESSION['Usuario_Nivel'] = !empty($DatosUsuario['NIVEL']) ? $DatosUsuario['NIVEL'] : 0;
    $_SESSION['Usuario_NombreNivel'] = !empty($DatosUsuario['NIVEL_NOMBRE']) ? $DatosUsuario['NIVEL_NOMBRE'] : '';
    $_SESSION['Usuario_Img'] = !empty($DatosUsuario['IMG']) ? $DatosUsuario['IMG'] : 'user.png';
    $_SESSION['Usuario_Saludo'] = !empty($DatosUsuario['SALUDO']) ? $DatosUsuario['SALUDO'] : 'Hola';
    $_SESSION['Usuario_Activo'] = !empty($DatosUsuario['ACTIVO']) ? $DatosUsuario['ACTIVO'] : 0;
}

/**
 * Archivo: funciones/funciones.php (línea 95).
 * Propósito: finalizar la sesión actual y limpiar los datos almacenados.
 * Cómo lo hace: vacía el arreglo global $_SESSION y luego destruye la sesión activa con session_destroy().
 * Devuelve: void porque su único efecto es cerrar la sesión del usuario.
 */
function CerrarSesionUsuario()
{
    $_SESSION = array();
    session_destroy();
}

/**
 * Archivo: funciones/funciones.php (línea 108).
 * Propósito: reconstruir los datos del usuario guardados en la sesión.
 * Cómo lo hace: verifica si existe el identificador de usuario en $_SESSION y arma un arreglo con los
 *              diferentes campos disponibles, devolviendo un array vacío si nadie inició sesión.
 * Devuelve: array con los datos mínimos del usuario autenticado o un array vacío si no hay sesión válida.
 */
function ObtenerUsuarioEnSesion()
{
    if (!empty($_SESSION['Usuario_ID'])) {
        $Usuario = array();
        $Usuario['id'] = $_SESSION['Usuario_ID'];
        $Usuario['apellido'] = !empty($_SESSION['Usuario_Apellido']) ? $_SESSION['Usuario_Apellido'] : '';
        $Usuario['nombre'] = !empty($_SESSION['Usuario_Nombre']) ? $_SESSION['Usuario_Nombre'] : '';
        $Usuario['usuario'] = !empty($_SESSION['Usuario_Usuario']) ? $_SESSION['Usuario_Usuario'] : '';
        $Usuario['id_nivel'] = !empty($_SESSION['Usuario_Nivel']) ? $_SESSION['Usuario_Nivel'] : 0;
        $Usuario['imagen'] = !empty($_SESSION['Usuario_Img']) ? $_SESSION['Usuario_Img'] : 'user.png';
        return $Usuario;
    }

    return array();
}

/**
 * Archivo: funciones/funciones.php (línea 131).
 * Propósito: asegurar que solo los usuarios autenticados puedan acceder a una página.
 * Cómo lo hace: comprueba si existe el identificador de usuario en la sesión y, de no existir,
 *              redirige automáticamente a la página de login mediante la función Redireccionar().
 * Devuelve: void porque únicamente aplica una validación y, en caso necesario, redirige.
 */
function RequiereSesion()
{
    if (empty($_SESSION['Usuario_ID'])) {
        Redireccionar('login.php');
    }
}

/**
 * Archivo: funciones/funciones.php (línea 144).
 * Propósito: comprobar de forma simple si hay un usuario autenticado.
 * Cómo lo hace: revisa si el índice 'Usuario_ID' en $_SESSION tiene un valor no vacío.
 * Devuelve: booleano true cuando existe usuario activo y false en caso contrario.
 */
function UsuarioEstaLogueado()
{
    return !empty($_SESSION['Usuario_ID']);
}

/**
 * Archivo: funciones/funciones.php (línea 156).
 * Propósito: generar una cadena con el nombre completo del usuario.
 * Cómo lo hace: toma los campos 'apellido' y 'nombre' del arreglo recibido y los concatena, respetando
 *              el formato "Apellido, Nombre" cuando ambos valores existen.
 * Devuelve: string con el nombre completo o el dato disponible si falta alguno de los dos campos.
 */
function NombreCompletoUsuario($Usuario)
{
    $Apellido = isset($Usuario['apellido']) ? $Usuario['apellido'] : '';
    $Nombre = isset($Usuario['nombre']) ? $Usuario['nombre'] : '';

    if ($Apellido != '' && $Nombre != '') {
        return $Apellido . ', ' . $Nombre;
    }

    return trim($Apellido . ' ' . $Nombre);
}

/**
 * Archivo: funciones/funciones.php (línea 175).
 * Propósito: traducir el identificador numérico de nivel a su nombre legible.
 * Cómo lo hace: compara el parámetro $IdNivel con los valores conocidos y devuelve la denominación
 *              correspondiente; si no coincide con ninguno, aplica un valor por defecto.
 * Devuelve: string con el nombre del nivel de permisos que se utilizará en la interfaz.
 */
function DenominacionNivel($IdNivel)
{
    if ($IdNivel == 1) {
        return 'Administrador';
    }

    if ($IdNivel == 2) {
        return 'Operador';
    }

    if ($IdNivel == 3) {
        return 'Chofer';
    }

    return 'Usuario';
}

/**
 * Archivo: funciones/funciones.php (línea 199).
 * Propósito: detallar las tareas que puede realizar un usuario según su nivel.
 * Cómo lo hace: evalúa el identificador de nivel y devuelve un texto descriptivo específico para cada
 *              caso, ofreciendo una explicación por defecto si el nivel no es reconocido.
 * Devuelve: string con la descripción de responsabilidades que se muestra en la interfaz.
 */
function DescripcionFuncionesNivel($IdNivel)
{
    if ($IdNivel == 1) {
        return 'transportes, choferes y viajes';
    }

    if ($IdNivel == 2) {
        return 'transportes y viajes';
    }

    if ($IdNivel == 3) {
        return 'el seguimiento de los viajes asignados';
    }

    return 'la información disponible en el panel';
}

/**
 * Archivo: funciones/funciones.php (línea 223).
 * Propósito: verificar si el usuario autenticado posee nivel de administrador.
 * Cómo lo hace: obtiene los datos de la sesión mediante ObtenerUsuarioEnSesion() y comprueba si el
 *              índice 'id_nivel' existe y coincide con el valor 1.
 * Devuelve: booleano true si la sesión pertenece a un administrador, false en caso contrario.
 */
function EsAdministrador()
{
    $Usuario = ObtenerUsuarioEnSesion();
    return !empty($Usuario['id_nivel']) && $Usuario['id_nivel'] == 1;
}

/**
 * Archivo: funciones/funciones.php (línea 235).
 * Propósito: determinar si el usuario autenticado tiene permisos de operador.
 * Cómo lo hace: consulta los datos almacenados en la sesión y evalúa si el campo 'id_nivel' es igual a 2.
 * Devuelve: booleano true para usuarios operadores y false para el resto.
 */
function EsOperador()
{
    $Usuario = ObtenerUsuarioEnSesion();
    return !empty($Usuario['id_nivel']) && $Usuario['id_nivel'] == 2;
}

/**
 * Archivo: funciones/funciones.php (línea 247).
 * Propósito: saber si el usuario actual está registrado como chofer.
 * Cómo lo hace: reutiliza ObtenerUsuarioEnSesion() para leer el nivel guardado y lo compara con el valor 3.
 * Devuelve: booleano true cuando la sesión corresponde a un chofer y false en los demás casos.
 */
function EsChofer()
{
    $Usuario = ObtenerUsuarioEnSesion();
    return !empty($Usuario['id_nivel']) && $Usuario['id_nivel'] == 3;
}

/**
 * Archivo: funciones/funciones.php (línea 260).
 * Propósito: obtener el listado de choferes activos desde la base de datos.
 * Cómo lo hace: ejecuta una consulta SELECT filtrando por nivel de usuario y estado activo,
 *              recorre el resultado y guarda cada fila en un arreglo numerado.
 * Devuelve: array con los choferes disponibles, incluyendo id, apellido, nombre y DNI.
 */
function Listar_Choferes($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, apellido, nombre, dni FROM usuarios WHERE id_nivel = 3 AND activo = 1 ORDER BY apellido, nombre";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['apellido'] = $data['apellido'];
            $Listado[$i]['nombre'] = $data['nombre'];
            $Listado[$i]['dni'] = $data['dni'];
            $i++;
        }
        mysqli_free_result($rs);
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 289).
 * Propósito: listar los transportes disponibles para asignar a viajes.
 * Cómo lo hace: realiza un SELECT que une transportes con marcas filtrando por disponibilidad,
 *              ordena los resultados y los almacena en un arreglo asociativo.
 * Devuelve: array con cada transporte y sus datos básicos (marca, modelo y patente).
 */
function Listar_Transportes($vConexion)
{
    $Listado = array();

    $SQL = "SELECT t.id, m.denominacion AS marca, t.modelo, t.patente"
        . " FROM transportes t, marcas m"
        . " WHERE m.id = t.marca_id"
        . " AND t.disponible = 1"
        . " ORDER BY m.denominacion, t.modelo, t.patente";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['marca'] = $data['marca'];
            $Listado[$i]['modelo'] = $data['modelo'];
            $Listado[$i]['patente'] = $data['patente'];
            $i++;
        }
        mysqli_free_result($rs);
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 322).
 * Propósito: cargar todas las marcas de vehículos registradas.
 * Cómo lo hace: consulta la tabla marcas ordenada alfabéticamente y copia cada fila al arreglo $Listado.
 * Devuelve: array con los identificadores y denominaciones de cada marca.
 */
function Listar_Marcas($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, denominacion FROM marcas ORDER BY denominacion";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['denominacion'] = $data['denominacion'];
            $i++;
        }
        mysqli_free_result($rs);
    }

    return $Listado;
}


/**
 * Archivo: funciones/funciones.php (línea 349).
 * Propósito: reunir los destinos disponibles para la planificación de viajes.
 * Cómo lo hace: ejecuta un SELECT sobre la tabla destinos y agrega cada fila a un arreglo de resultados.
 * Devuelve: array con id y denominación de todos los destinos ordenados alfabéticamente.
 */
function Listar_Destinos($vConexion)
{
    $Listado = array();

    $SQL = "SELECT id, denominacion FROM destinos ORDER BY denominacion";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['denominacion'] = $data['denominacion'];
            $i++;
        }
        mysqli_free_result($rs);
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 376).
 * Propósito: revisar que los datos enviados para crear un chofer sean correctos.
 * Cómo lo hace: lee los valores del formulario ($_POST), aplica validaciones de formato y existencia en
 *              base de datos (usuario y DNI) y acumula mensajes de error cuando encuentra problemas.
 * Devuelve: string con todos los errores detectados; si está vacío significa que la información es válida.
 */
function Validar_Datos_Chofer($vConexion)
{
    $Mensaje = '';

    $Apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $Nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $Dni = isset($_POST['dni']) ? trim($_POST['dni']) : '';
    $Usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $Clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

    if (strlen($Apellido) < 2) {
        $Mensaje .= 'Debes ingresar un apellido con al menos 2 caracteres. <br />';
    }

    if (strlen($Nombre) < 2) {
        $Mensaje .= 'Debes ingresar un nombre con al menos 2 caracteres. <br />';
    }

    if ($Dni === '') {
        $Mensaje .= 'Debes ingresar el DNI. <br />';
    } else {
        if (!ctype_digit($Dni) || strlen($Dni) < 7 || strlen($Dni) > 8) {
            $Mensaje .= 'El DNI debe tener 7 u 8 dígitos numéricos. <br />';
        } else {
            if (ExisteDNI($Dni, $vConexion)) {
                $Mensaje .= 'El DNI ingresado ya se encuentra registrado. <br />';
            }
        }
    }

    if ($Usuario === '') {
        $Mensaje .= 'Debes ingresar el usuario. <br />';
    } else {
        $Usuario = strtolower($Usuario);
        $Permitidos = '._-';
        $UsuarioValido = true;
        for ($i = 0; $i < strlen($Usuario); $i++) {
            $Caracter = $Usuario[$i];
            if (!ctype_alnum($Caracter) && strpos($Permitidos, $Caracter) === false) {
                $UsuarioValido = false;
                break;
            }
        }

        if (!$UsuarioValido) {
            $Mensaje .= 'El usuario solo puede tener letras, números, puntos, guiones o guiones bajos. <br />';
        } elseif (ExisteUsuario($Usuario, $vConexion)) {
            $Mensaje .= 'El usuario ingresado ya existe. <br />';
        }
    }

    if ($Clave === '') {
        $Mensaje .= 'Debes ingresar la clave. <br />';
    } elseif (strlen($Clave) < 5) {
        $Mensaje .= 'La clave debe tener al menos 5 caracteres. <br />';
    }

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    if ($Usuario !== '') {
        $_POST['usuario'] = $Usuario;
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 451).
 * Propósito: comprobar que los datos de un transporte nuevo sean coherentes antes de guardarlos.
 * Cómo lo hace: valida la existencia de la marca, el formato del modelo, año y patente, y que la patente no
 *              esté registrada; también normaliza los valores recibidos del formulario.
 * Devuelve: string con los mensajes de error generados; sin contenido indica que los datos son correctos.
 */
function Validar_Datos_Transporte($vConexion)
{
    $Mensaje = '';

    $MarcaId = isset($_POST['marca_id']) ? (int) $_POST['marca_id'] : 0;
    $Modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
    $Anio = isset($_POST['anio']) ? trim($_POST['anio']) : '';
    $Patente = isset($_POST['patente']) ? strtoupper(str_replace(' ', '', $_POST['patente'])) : '';

    if ($MarcaId == 0) {
        $Mensaje .= 'Debes seleccionar una marca. <br />';
    } else {
        $SQL = "SELECT id FROM marcas WHERE id = " . $MarcaId;
        $rs = mysqli_query($vConexion, $SQL);
        if ($rs != false) {
            $data = mysqli_fetch_array($rs);
            if (empty($data)) {
                $Mensaje .= 'La marca seleccionada no es válida. <br />';
            }
            mysqli_free_result($rs);
        }
    }

    if (strlen($Modelo) < 2) {
        $Mensaje .= 'Debes ingresar el modelo. <br />';
    }

    if ($Anio !== '') {
        if (!ctype_digit($Anio) || strlen($Anio) != 4) {
            $Mensaje .= 'El año debe tener 4 dígitos. <br />';
        } else {
            $AnioEntero = (int) $Anio;
            $AnioActual = (int) date('Y');
            if ($AnioEntero < 1990 || $AnioEntero > $AnioActual + 1) {
                $Mensaje .= 'El año debe estar entre 1990 y ' . ($AnioActual + 1) . '. <br />';
            }
        }
    }

    if ($Patente === '') {
        $Mensaje .= 'Debes ingresar la patente. <br />';
    } else {
        $LongitudPatente = strlen($Patente);
        if ($LongitudPatente < 6 || $LongitudPatente > 7 || !ctype_alnum($Patente)) {
            $Mensaje .= 'La patente debe tener entre 6 y 7 caracteres alfanuméricos. <br />';
        } elseif (ExistePatente($Patente, $vConexion)) {
            $Mensaje .= 'La patente ingresada ya se encuentra registrada. <br />';
        }
    }

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    if ($Patente !== '') {
        $_POST['patente'] = $Patente;
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 519).
 * Propósito: garantizar que la programación de un viaje contenga referencias válidas y montos correctos.
 * Cómo lo hace: valida la existencia de chofer, transporte y destino, revisa el formato de la fecha y el
 *              costo, normaliza los datos y acumula cada error encontrado para informar al usuario.
 * Devuelve: string con el detalle de errores; una cadena vacía implica que el viaje puede registrarse.
 */
function Validar_Datos_Viaje($vConexion)
{
    $Mensaje = '';

    $ChoferId = isset($_POST['chofer_id']) ? (int) $_POST['chofer_id'] : 0;
    $TransporteId = isset($_POST['transporte_id']) ? (int) $_POST['transporte_id'] : 0;
    $DestinoId = isset($_POST['destino_id']) ? (int) $_POST['destino_id'] : 0;
    $Fecha = isset($_POST['fecha_programada']) ? trim($_POST['fecha_programada']) : '';
    $CostoIngresado = isset($_POST['costo']) ? trim($_POST['costo']) : '';
    $Porcentaje = isset($_POST['porcentaje_chofer']) ? trim($_POST['porcentaje_chofer']) : '';

    if ($ChoferId == 0) {
        $Mensaje .= 'Debes seleccionar un chofer. <br />';
    } else {
        $SQL = "SELECT id FROM usuarios WHERE id = " . $ChoferId . " AND id_nivel = 3 AND activo = 1";
        $rs = mysqli_query($vConexion, $SQL);
        if ($rs != false) {
            $data = mysqli_fetch_array($rs);
            if (empty($data)) {
                $Mensaje .= 'El chofer seleccionado no es válido. <br />';
            }
            mysqli_free_result($rs);
        }
    }

    if ($TransporteId == 0) {
        $Mensaje .= 'Debes seleccionar un transporte. <br />';
    } else {
        $SQL = "SELECT id FROM transportes WHERE id = " . $TransporteId . " AND disponible = 1";
        $rs = mysqli_query($vConexion, $SQL);
        if ($rs != false) {
            $data = mysqli_fetch_array($rs);
            if (empty($data)) {
                $Mensaje .= 'El transporte seleccionado no es válido. <br />';
            }
            mysqli_free_result($rs);
        }
    }

    if ($DestinoId == 0) {
        $Mensaje .= 'Debes seleccionar un destino. <br />';
    } else {
        $SQL = "SELECT id FROM destinos WHERE id = " . $DestinoId;
        $rs = mysqli_query($vConexion, $SQL);
        if ($rs != false) {
            $data = mysqli_fetch_array($rs);
            if (empty($data)) {
                $Mensaje .= 'El destino seleccionado no es válido. <br />';
            }
            mysqli_free_result($rs);
        }
    }

    if ($Fecha === '') {
        $Mensaje .= 'Debes ingresar la fecha programada. <br />';
    } else {
        $Partes = explode('/', $Fecha);
        if (count($Partes) != 3) {
            $Mensaje .= 'La fecha debe tener el formato dd/mm/aaaa. <br />';
        } else {
            $Dia = (int) $Partes[0];
            $Mes = (int) $Partes[1];
            $Anio = (int) $Partes[2];
            if (!checkdate($Mes, $Dia, $Anio)) {
                $Mensaje .= 'La fecha ingresada no es válida. <br />';
            } else {
                $_POST['fecha_sql'] = sprintf('%04d-%02d-%02d', $Anio, $Mes, $Dia);
            }
        }
    }

    if ($CostoIngresado === '') {
        $Mensaje .= 'Debes ingresar el costo del viaje. <br />';
    } else {
        $CostoNormalizado = str_replace('.', '', $CostoIngresado);
        $CostoNormalizado = str_replace(',', '.', $CostoNormalizado);
        if (!is_numeric($CostoNormalizado) || (float) $CostoNormalizado <= 0) {
            $Mensaje .= 'El costo debe ser un número mayor a 0. <br />';
        } else {
            $_POST['costo_normalizado'] = (float) $CostoNormalizado;
        }
    }

    if ($Porcentaje === '') {
        $Mensaje .= 'Debes ingresar el porcentaje del chofer. <br />';
    } else {
        if (!ctype_digit($Porcentaje)) {
            $Mensaje .= 'El porcentaje debe ser un número entero. <br />';
        } else {
            $Valor = (int) $Porcentaje;
            if ($Valor < 0 || $Valor > 100) {
                $Mensaje .= 'El porcentaje debe estar entre 0 y 100. <br />';
            }
        }
    }

    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }

    return $Mensaje;
}

/**
 * Archivo: funciones/funciones.php (línea 629).
 * Propósito: guardar un nuevo chofer en la tabla de usuarios.
 * Cómo lo hace: toma los datos normalizados del formulario, arma una sentencia INSERT con el nivel de
 *              chofer (3) y ejecuta la consulta; si falla aborta la ejecución con un mensaje de error.
 * Devuelve: booleano true cuando la inserción se realiza correctamente; detiene el script si ocurre un error.
 */
function Insertar_Chofer($vConexion)
{
    $Apellido = isset($_POST['apellido']) ? $_POST['apellido'] : '';
    $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $Dni = isset($_POST['dni']) ? $_POST['dni'] : '';
    $Usuario = isset($_POST['usuario']) ? strtolower($_POST['usuario']) : '';
    $Clave = isset($_POST['clave']) ? $_POST['clave'] : '';

    $SQL = "INSERT INTO usuarios (apellido, nombre, dni, usuario, clave, activo, id_nivel, fecha_creacion)"
        . " VALUES ('" . $Apellido . "', '" . $Nombre . "', '" . $Dni . "', '" . $Usuario . "', '" . $Clave . "', 1, 3, NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 654).
 * Propósito: crear un registro de transporte disponible en la base de datos.
 * Cómo lo hace: extrae los campos del formulario, construye una consulta INSERT sobre la tabla transportes,
 *              incluyendo la fecha de creación y disponibilidad, y la ejecuta con la conexión recibida.
 * Devuelve: booleano true si la inserción fue exitosa; de fallar, la función corta la ejecución con un die().
 */
function Insertar_Transporte($vConexion)
{
    $Marca = isset($_POST['marca_id']) ? (int) $_POST['marca_id'] : 0;
    $Modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
    $Patente = isset($_POST['patente']) ? $_POST['patente'] : '';
    $Anio = isset($_POST['anio']) ? (int) $_POST['anio'] : 0;
    $Disponible = !empty($_POST['disponible']) ? 1 : 0;

    $SQL = "INSERT INTO transportes (marca_id, modelo, patente, anio, disponible, fecha_creacion)"
        . " VALUES (" . $Marca . ", '" . $Modelo . "', '" . $Patente . "', " . $Anio . ", " . $Disponible . ", NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 679).
 * Propósito: registrar un viaje nuevo asociando chofer, transporte y destino.
 * Cómo lo hace: obtiene los identificadores normalizados del formulario, arma la sentencia INSERT para la
 *              tabla viajes incluyendo costos, porcentaje y el usuario creador, y la ejecuta.
 * Devuelve: booleano true cuando el viaje se graba correctamente; ante errores detiene la ejecución.
 */
function Insertar_Viaje($vConexion)
{
    $Chofer = isset($_POST['chofer_id']) ? (int) $_POST['chofer_id'] : 0;
    $Transporte = isset($_POST['transporte_id']) ? (int) $_POST['transporte_id'] : 0;
    $Destino = isset($_POST['destino_id']) ? (int) $_POST['destino_id'] : 0;
    $Fecha = !empty($_POST['fecha_sql']) ? $_POST['fecha_sql'] : '';
    $Costo = !empty($_POST['costo_normalizado']) ? (float) $_POST['costo_normalizado'] : 0;
    $Porcentaje = isset($_POST['porcentaje_chofer']) ? (int) $_POST['porcentaje_chofer'] : 0;
    $CreadoPor = !empty($_SESSION['Usuario_ID']) ? (int) $_SESSION['Usuario_ID'] : 'NULL';

    $SQL = "INSERT INTO viajes (chofer_id, transporte_id, fecha_programada, destino_id, costo, porcentaje_chofer, creado_por, fecha_creacion)"
        . " VALUES (" . $Chofer . ", " . $Transporte . ", '" . $Fecha . "', " . $Destino . ", " . $Costo . ", " . $Porcentaje . ", " . $CreadoPor . ", NOW())";

    if (!mysqli_query($vConexion, $SQL)) {
        die('No se pudo ejecutar la inserción.');
    }

    return true;
}

/**
 * Archivo: funciones/funciones.php (línea 706).
 * Propósito: consultar los viajes programados, opcionalmente filtrados por chofer.
 * Cómo lo hace: construye un SELECT que une viajes con choferes, transportes, marcas y destinos, aplica un
 *              filtro adicional si se recibe $ChoferId y recorre el resultado acumulando cada fila en un arreglo.
 * Devuelve: array con los viajes y toda la información necesaria para mostrarlos en listados.
 */
function Listar_Viajes($vConexion, $ChoferId = null)
{
    $Listado = array();

    $SQL = "SELECT v.id, v.fecha_programada, d.denominacion AS destino, v.costo, v.porcentaje_chofer,"
        . " c.apellido AS chofer_apellido, c.nombre AS chofer_nombre, c.dni AS chofer_dni,"
        . " m.denominacion AS marca, t.modelo, t.patente"
        . " FROM viajes v, usuarios c, transportes t, marcas m, destinos d"
        . " WHERE c.id = v.chofer_id"
        . " AND t.id = v.transporte_id"
        . " AND m.id = t.marca_id"
        . " AND d.id = v.destino_id";

    if (!empty($ChoferId)) {
        $SQL .= " AND v.chofer_id = " . $ChoferId;
    }

    $SQL .= " ORDER BY v.fecha_programada, d.denominacion";

    $rs = mysqli_query($vConexion, $SQL);

    if ($rs != false) {
        $i = 0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['id'] = $data['id'];
            $Listado[$i]['fecha_programada'] = $data['fecha_programada'];
            $Listado[$i]['destino'] = $data['destino'];
            $Listado[$i]['costo'] = $data['costo'];
            $Listado[$i]['porcentaje_chofer'] = $data['porcentaje_chofer'];
            $Listado[$i]['chofer_apellido'] = $data['chofer_apellido'];
            $Listado[$i]['chofer_nombre'] = $data['chofer_nombre'];
            $Listado[$i]['chofer_dni'] = $data['chofer_dni'];
            $Listado[$i]['marca'] = $data['marca'];
            $Listado[$i]['modelo'] = $data['modelo'];
            $Listado[$i]['patente'] = $data['patente'];
            $i++;
        }
        mysqli_free_result($rs);
    }

    return $Listado;
}

/**
 * Archivo: funciones/funciones.php (línea 755).
 * Propósito: determinar si un nombre de usuario ya está registrado en la base de datos.
 * Cómo lo hace: ejecuta una consulta SELECT filtrando por el texto del usuario y verifica si devuelve filas.
 * Devuelve: booleano true cuando encuentra coincidencias; false si el usuario está disponible o ante errores.
 */
function ExisteUsuario($Usuario, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE usuario = '" . $Usuario . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);
    mysqli_free_result($rs);

    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 776).
 * Propósito: validar si un DNI ya se encuentra asociado a otro usuario.
 * Cómo lo hace: realiza un SELECT sobre la tabla de usuarios buscando el DNI exacto y evalúa si retorna registros.
 * Devuelve: booleano true si el DNI está en uso; false cuando no se encuentra o si ocurre un error en la consulta.
 */
function ExisteDNI($Dni, $vConexion)
{
    $SQL = "SELECT id FROM usuarios WHERE dni = '" . $Dni . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);
    mysqli_free_result($rs);

    return !empty($Existe);
}

/**
 * Archivo: funciones/funciones.php (línea 797).
 * Propósito: comprobar si una patente de transporte ya fue registrada.
 * Cómo lo hace: ejecuta una consulta SELECT filtrando por la patente enviada y analiza si existen filas resultantes.
 * Devuelve: booleano true cuando la patente está ocupada; false si no se encuentra o si la consulta falla.
 */
function ExistePatente($Patente, $vConexion)
{
    $SQL = "SELECT id FROM transportes WHERE patente = '" . $Patente . "'";
    $rs = mysqli_query($vConexion, $SQL);

    if ($rs == false) {
        return false;
    }

    $Existe = mysqli_fetch_array($rs);
    mysqli_free_result($rs);

    return !empty($Existe);
}

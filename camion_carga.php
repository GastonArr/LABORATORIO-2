<?php
/**
 * Pantalla de registro de camiones/transportes.
 * Este archivo combina lógica de validación con la construcción del formulario HTML.
 * La idea es documentar línea por línea para que puedas estudiar cada paso del flujo.
 */

// Se importa el archivo que conoce cómo conectarse a la base de datos.
require_once 'funciones/conexion.php';
// Se traen las funciones auxiliares compartidas (manejo de sesiones, validaciones, etc.).
require_once 'funciones/funciones.php';
// Antes de mostrar nada verificamos que exista una sesión activa, de lo contrario se redirige al login.
RequiereSesion();

// Si el usuario logueado es un chofer, lo sacamos de esta pantalla porque sólo el administrador puede registrar transportes.
if (EsChofer()) {
    // Se hace la redirección al inicio utilizando la función helper que envía la cabecera Location.
    Redireccionar('index.php');
}

// Se crea un objeto PDO listo para ejecutar consultas contra la base.
$MiConexion = ConexionBD();

// Variables compartidas con el layout para definir título de la pestaña y opción activa del menú.
$pageTitle = 'Registrar un nuevo transporte';
$activePage = 'camion_carga';

// Se piden todas las marcas disponibles; esto alimentará el <select> del formulario.
$marcas = Listar_Marcas($MiConexion);

// Variables de control para mostrar mensajes de validación (texto y estilo de la alerta).
$Mensaje = '';
$Estilo = 'warning';

// Cuando el formulario llega por POST con el botón “Registrar” se procede a validar y guardar.
if (!empty($_POST['BotonRegistrar'])) {
    // Se valida cada campo del transporte y se recibe un mensaje con el primer error encontrado.
    $Mensaje = Validar_Datos_Transporte($MiConexion);
    // Si no hubo errores se pasa a insertar el registro.
    if (empty($Mensaje)) {
        // Insertar_Transporte devuelve false en caso de error; de lo contrario quedó guardado correctamente.
        if (Insertar_Transporte($MiConexion) != false) {
            // Se notifica al usuario que el alta fue exitosa.
            $Mensaje = 'Se ha registrado correctamente.';
            // Se limpian los datos enviados para que los campos vuelvan vacíos.
            $_POST = array();
            // El mensaje exitoso se pinta en color verde mediante la clase alert-success.
            $Estilo = 'success';
        }
    }
}

// Variables de conveniencia para rellenar nuevamente los inputs cuando hay errores de validación.
$CantidadMarcas = count($marcas);
$MarcaSeleccionada = !empty($_POST['marca_id']) ? $_POST['marca_id'] : '';
$ModeloValor = !empty($_POST['modelo']) ? $_POST['modelo'] : '';
$AnioValor = !empty($_POST['anio']) ? $_POST['anio'] : '';
$PatenteValor = !empty($_POST['patente']) ? $_POST['patente'] : '';
// El checkbox se marca por defecto salvo que el usuario lo haya desmarcado explícitamente en el POST.
$DisponibleMarcado = !empty($_POST) ? !empty($_POST['disponible']) : true;

// Se cargan las piezas del layout (cabecera, barra superior y menú lateral) antes del contenido principal.
require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<!-- Sección principal del template de administración -->
<main id="main" class="main">
    <!-- Área que muestra el título y las migas de pan para orientar al usuario -->
    <div class="pagetitle">
        <!-- Título visible del formulario -->
        <h1>Registrar un nuevo transporte</h1>
        <!-- Navegación jerárquica (breadcrumb) -->
        <nav>
            <ol class="breadcrumb">
                <!-- Link de regreso al dashboard -->
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <!-- Nivel intermedio indicando la sección -->
                <li class="breadcrumb-item">Transportes</li>
                <!-- Último nivel marcando la pantalla actual -->
                <li class="breadcrumb-item active">Carga</li>
            </ol>
        </nav>
    </div>
    <!-- Contenedor de Bootstrap para centrar el formulario -->
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Subtítulo que guía al usuario -->
                        <h5 class="card-title">Ingresa los datos</h5>
                        <!-- Alerta informativa permanente con las indicaciones generales -->
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                        </div>
                        <!-- Bloque condicional que muestra los mensajes de error o éxito -->
                        <?php if (!empty($Mensaje)) { ?>
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php } ?>
                        <!-- Formulario principal: method POST para enviar los datos al mismo script -->
                        <form class="row g-3" method="post" action="" novalidate>
                            <!-- Selector de marca -->
                            <div class="col-12">
                                <label for="marca_id" class="form-label">Marca (*)</label>
                                <select class="form-select" id="marca_id" name="marca_id" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Se recorre el arreglo de marcas para armar cada <option>.
                                    for ($i = 0; $i < $CantidadMarcas; $i++) {
                                        // Se determina si la marca actual coincide con la que el usuario había elegido.
                                        $Seleccionado = (!empty($MarcaSeleccionada) && $MarcaSeleccionada == $marcas[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <!-- Cada opción usa el id de la marca como value y la denominación como texto visible -->
                                        <option value="<?php echo $marcas[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $marcas[$i]['denominacion']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- Campo de texto para el modelo -->
                            <div class="col-12">
                                <label for="modelo" class="form-label">Modelo (*)</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo !empty($ModeloValor) ? htmlspecialchars($ModeloValor) : ''; ?>" required>
                            </div>
                            <!-- Campo opcional para el año del vehículo -->
                            <div class="col-6">
                                <label for="anio" class="form-label">Año</label>
                                <input type="text" class="form-control" id="anio" name="anio" value="<?php echo !empty($AnioValor) ? htmlspecialchars($AnioValor) : ''; ?>" placeholder="AAAA">
                            </div>
                            <!-- Campo obligatorio para la patente -->
                            <div class="col-6">
                                <label for="patente" class="form-label">Patente (*)</label>
                                <input type="text" class="form-control" id="patente" name="patente" value="<?php echo !empty($PatenteValor) ? htmlspecialchars($PatenteValor) : ''; ?>" required>
                            </div>
                            <!-- Checkbox que permite desactivar el vehículo -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="disponible" name="disponible" <?php echo $DisponibleMarcado ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="disponible">
                                        Habilitado
                                    </label>
                                </div>
                            </div>
                            <!-- Botones de acción del formulario -->
                            <div class="text-center">
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <a href="camion_carga.php" class="btn btn-secondary">Limpiar Campos</a>
                                <a href="index.php" class="text-primary fw-bold">Volver al index</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Se completa el layout con el pie que cierra las etiquetas HTML abiertas en el header.
require_once 'includes/footer.php';
?>

<?php
/**
 * Formulario de alta de choferes.
 * Se agrega documentación detallada para comprender cada paso del proceso.
 */

// Archivo de conexión (crea el objeto PDO a la base de datos configurada).
require_once 'funciones/conexion.php';
// Biblioteca con funciones compartidas (gestión de sesión, validaciones, helpers, etc.).
require_once 'funciones/funciones.php';
// Previene que usuarios sin login vean la pantalla.
RequiereSesion();

// Solamente los administradores están habilitados para crear choferes.
if (!EsAdministrador()) {
    // Si no es admin, lo devolvemos al inicio inmediatamente.
    Redireccionar('index.php');
}

// Obtenemos el recurso de conexión para usar en consultas posteriores.
$MiConexion = ConexionBD();

// Datos utilizados por el layout para título y opción seleccionada en el menú.
$pageTitle = 'Registrar un nuevo chofer';
$activePage = 'choferes';

// Variables que controlan el texto y estilo del mensaje mostrado en pantalla.
$Mensaje = '';
$Estilo = 'warning';

// Cuando se envía el formulario se ejecuta la validación y, si todo está correcto, se guarda el chofer.
if (!empty($_POST['BotonRegistrar'])) {
    // La validación retorna el mensaje de error (o cadena vacía si todo está perfecto).
    $Mensaje = Validar_Datos_Chofer($MiConexion);
    if (empty($Mensaje)) {
        // Insertar_Chofer devuelve false ante fallos, por eso se verifica que el resultado sea distinto de false.
        if (Insertar_Chofer($MiConexion) != false) {
            // Mensaje para confirmar al usuario que se guardó correctamente.
            $Mensaje = 'Se ha registrado correctamente.';
            // Se limpian los valores del formulario para mostrar campos vacíos luego del éxito.
            $_POST = array();
            // Establecemos el estilo de alerta verde.
            $Estilo = 'success';
        }
    }
}

// Se suman las piezas comunes del layout (header, topbar, sidebar) antes del contenido propio.
require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<!-- Estructura principal del panel -->
<main id="main" class="main">
    <!-- Título y breadcrumb de navegación -->
    <div class="pagetitle">
        <h1>Registrar un nuevo chofer</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Transportes</li>
                <li class="breadcrumb-item active">Carga Chofer</li>
            </ol>
        </nav>
    </div>
    <!-- Contenedor del formulario -->
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <!-- Texto introductorio -->
                        <h5 class="card-title">Ingresa los datos</h5>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                        </div>
                        <!-- Muestra mensajes de validación dinámicos -->
                        <?php if (!empty($Mensaje)) { ?>
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php } ?>
                        <!-- Formulario que envía los datos al mismo archivo -->
                        <form class="row g-3" method="post" action="" novalidate>
                            <!-- Campo Apellido -->
                            <div class="col-12">
                                <label for="apellido" class="form-label">Apellido (*)</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo !empty($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required>
                            </div>
                            <!-- Campo Nombre -->
                            <div class="col-12">
                                <label for="nombre" class="form-label">Nombre (*)</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo !empty($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                            </div>
                            <!-- Campo DNI -->
                            <div class="col-12">
                                <label for="dni" class="form-label">DNI (*)</label>
                                <input type="text" class="form-control" id="dni" name="dni" value="<?php echo !empty($_POST['dni']) ? htmlspecialchars($_POST['dni']) : ''; ?>" required>
                            </div>
                            <!-- Campo Usuario -->
                            <div class="col-12">
                                <label for="usuario" class="form-label">Usuario (*)</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo !empty($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required>
                            </div>
                            <!-- Campo Clave -->
                            <div class="col-12">
                                <label for="clave" class="form-label">Clave (*)</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>
                            <!-- Botonera -->
                            <div class="text-center">
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <a href="chofer_carga.php" class="btn btn-secondary">Limpiar Campos</a>
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
// Footer compartido del panel (cierra etiquetas abiertas y agrega scripts comunes).
require_once 'includes/footer.php';
?>

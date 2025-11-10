<?php
/**
 * Formulario de autenticación del panel.
 * El objetivo es explicar cada paso para entender cómo funciona el login.
 */

// Dependencias necesarias para conectarse a la base y usar funciones compartidas.
require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php';

// Si alguien ya inició sesión se evita mostrar otra vez el login y se lo redirige al dashboard.
if (UsuarioEstaLogueado()) {
    Redireccionar('index.php');
}

// Creamos la conexión para poder consultar usuarios.
$MiConexion = ConexionBD();

// Variables utilizadas por la plantilla y para almacenar mensajes de error.
$pageTitle = 'Panel de Administración - Login';
$Mensaje = '';

// Al enviar el formulario (botón Login) se ejecuta la validación.
if (!empty($_POST['BotonLogin'])) {
    // Se normaliza el usuario en minúsculas y se remueven espacios.
    $usuario = !empty($_POST['usuario']) ? strtolower(trim($_POST['usuario'])) : '';
    // La clave solo se recorta para evitar espacios accidentales.
    $clave = !empty($_POST['clave']) ? trim($_POST['clave']) : '';

    // Validación básica: ambos campos deben venir cargados.
    if ($usuario === '' || $clave === '') {
        $Mensaje = 'Debes ingresar el usuario y la clave.';
    } else {
        // Consulta a la base para verificar si los datos son válidos.
        $UsuarioLogueado = DatosLogin($usuario, $clave, $MiConexion);

        if (!empty($UsuarioLogueado)) {
            // Si el usuario existe pero está marcado como inactivo se informa la situación.
            if (isset($UsuarioLogueado['ACTIVO']) && $UsuarioLogueado['ACTIVO'] == 0) {
                $Mensaje = 'Ud. no se encuentra activo en el sistema.';
            } else {
                // Credenciales correctas: se guarda la sesión y se va al panel principal.
                GuardarSesionUsuario($UsuarioLogueado);
                Redireccionar('index.php');
            }
        } else {
            // Caso en que usuario/clave no coinciden.
            $Mensaje = 'Datos incorrectos, ingresa nuevamente.';
        }
    }
}

// Se incluye el header del template que abre el <html> y aplica estilos.
require_once 'includes/header.php';
?>
<main>
    <div class="container">
        <!-- Sección centrada vertical y horizontalmente -->
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <!-- Encabezado con el logo del sistema -->
                        <div class="d-flex justify-content-center py-4">
                            <a href="login.php" class="logo d-flex align-items-center w-auto">
                                <img src="assets/img/logo.png" alt="">
                                <span class="d-none d-lg-block">Panel de Administración</span>
                            </a>
                        </div>
                        <!-- Tarjeta Bootstrap que contiene el formulario -->
                        <div class="card mb-3 w-100">
                            <div class="card-body">
                                <div class="pt-4 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Ingresa tu cuenta</h5>
                                    <p class="text-center small">Ingresa tus datos de usuario y clave</p>
                                </div>
                                <!-- Se muestran mensajes dinámicos según exista error o simplemente información -->
                                <?php if (!empty($Mensaje)): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <?php echo htmlspecialchars($Mensaje); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info" role="alert">
                                        <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                                    </div>
                                <?php endif; ?>
                                <!-- Formulario que envía los datos al mismo script -->
                                <form class="row g-3" method="post" action="" novalidate>
                                    <div class="col-12">
                                        <label for="usuario" class="form-label">Usuario (*)</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                            <input type="text" name="usuario" class="form-control" id="usuario" value="<?php echo !empty($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="clave" class="form-label">Clave (*)</label>
                                        <input type="password" name="clave" class="form-control" id="clave" required>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100" type="submit" name="BotonLogin" value="Login">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Créditos del template original -->
                        <div class="credits">
                            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
<?php
// Footer compartido que cierra el documento.
require_once 'includes/footer.php';
?>

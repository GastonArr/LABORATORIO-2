<?php
/**
 * Dashboard principal. Aquí se contextualiza al usuario luego del inicio de sesión.
 */

// Dependencia para acceder a la base de datos.
require_once 'funciones/conexion.php';
// Librería que agrupa helpers generales (manejo de sesión, funciones de usuario, etc.).
require_once 'funciones/funciones.php';
// Se exige sesión activa; de lo contrario, se corta el flujo.
RequiereSesion();

// El layout necesita saber el título y la opción activa del sidebar.
$pageTitle = 'Panel de Administración';
$activePage = 'dashboard';
// Se obtiene el array asociativo con los datos del usuario guardados en la sesión.
$user = ObtenerUsuarioEnSesion();
// Helper que arma "Nombre Apellido" a partir de los campos básicos.
$userFullName = NombreCompletoUsuario($user);
// Inicializamos el nivel en null por si el usuario no tiene datos completos.
$userNivel = null;
if (isset($user['id_nivel'])) {
    // Si está definido el nivel se lo asignamos para reutilizarlo más adelante.
    $userNivel = $user['id_nivel'];
}
// Texto con la denominación humana del nivel (Administrador, Chofer, etc.).
$userDenominacion = DenominacionNivel($userNivel);
// Lista de funcionalidades que el usuario puede realizar según su nivel.
$funcionesPermitidas = DescripcionFuncionesNivel($userNivel);

// Layout base reutilizable en todo el panel.
require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<!-- Contenido central del dashboard -->
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Bienvenido</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Panel</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Mensaje personalizado para el usuario logueado -->
                        <h5 class="card-title">Hola, <?php echo htmlspecialchars($userFullName); ?> (<?php echo htmlspecialchars($userDenominacion); ?>)!</h5>
                        <p class="card-text">Desde este panel podrás gestionar la operación diaria del sistema. Según tu función, podrás gestionar: <?php echo htmlspecialchars($funcionesPermitidas); ?>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Footer reutilizable que incluye scripts comunes y cierra el documento HTML.
require_once 'includes/footer.php';
?>

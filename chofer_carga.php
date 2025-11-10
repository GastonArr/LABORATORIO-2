<?php
require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php';
RequiereSesion();

if (!EsAdministrador()) { // Se verifica que solamente los administradores puedan cargar nuevos choferes, respetando los niveles de acceso.
    Redireccionar('index.php'); // Si el usuario no es administrador se lo redirige al panel principal para impedir el acceso.
}

$MiConexion = ConexionBD();

$pageTitle = 'Registrar un nuevo chofer';
$activePage = 'choferes';

$Mensaje = '';
$Estilo = 'warning';

if (!empty($_POST['BotonRegistrar'])) {
    $Mensaje = Validar_Datos_Chofer($MiConexion);
    if (empty($Mensaje)) {
        if (Insertar_Chofer($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array();
            $Estilo = 'success';
        }
    }
}

require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<main id="main" class="main">
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
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ingresa los datos</h5>
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-1"></i> Los campos indicados con (*) son requeridos
                        </div>
                        <?php if (!empty($Mensaje)) { ?>
                            <div class="alert alert-<?php echo $Estilo; ?>" role="alert">
                                <?php echo $Mensaje; ?>
                            </div>
                        <?php } ?>
                        <form class="row g-3" method="post" action="" novalidate>
                            <div class="col-12">
                                <label for="apellido" class="form-label">Apellido (*)</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo !empty($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="nombre" class="form-label">Nombre (*)</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo !empty($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="dni" class="form-label">DNI (*)</label>
                                <input type="text" class="form-control" id="dni" name="dni" value="<?php echo !empty($_POST['dni']) ? htmlspecialchars($_POST['dni']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="usuario" class="form-label">Usuario (*)</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo !empty($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="clave" class="form-label">Clave (*)</label>
                                <input type="password" class="form-control" id="clave" name="clave" required>
                            </div>
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
<?php require_once 'includes/footer.php'; ?>

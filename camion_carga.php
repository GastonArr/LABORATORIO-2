<?php
require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php'; // Se incluye el archivo de funciones para disponer de la conexión, utilidades y sesiones.
RequiereSesion(); // Se asegura que solo usuarios autenticados puedan acceder a la pantalla.

if (EsChofer()) { // Se controla que los choferes no puedan cargar transportes como lo indica la consigna.
    Redireccionar('index.php'); // En caso de que un chofer llegue a la URL, se lo redirige al panel principal.
}

$MiConexion = ConexionBD();

$pageTitle = 'Registrar un nuevo transporte'; // Se define el título de la página para el encabezado HTML.
$activePage = 'camion_carga'; // Se establece el identificador de la página activa para resaltar el menú lateral correspondiente.

$marcas = Listar_Marcas($MiConexion); // Se obtienen todas las marcas registradas para alimentar el selector del formulario.

$Mensaje = '';
$Estilo = 'warning';

if (!empty($_POST['BotonRegistrar'])) {
    $Mensaje = Validar_Datos_Transporte($MiConexion);
    if (empty($Mensaje)) {
        if (Insertar_Transporte($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array();
            $Estilo = 'success';
        }
    }
}

$CantidadMarcas = count($marcas);
$MarcaSeleccionada = !empty($_POST['marca_id']) ? $_POST['marca_id'] : '';
$ModeloValor = !empty($_POST['modelo']) ? $_POST['modelo'] : '';
$AnioValor = !empty($_POST['anio']) ? $_POST['anio'] : '';
$PatenteValor = !empty($_POST['patente']) ? $_POST['patente'] : '';
$DisponibleMarcado = !empty($_POST) ? !empty($_POST['disponible']) : true;

require_once 'includes/header.php'; // Se carga el encabezado común del panel.
require_once 'includes/topbar.php'; // Se incluye la barra superior con los datos del usuario logueado.
require_once 'includes/sidebar.php'; // Se incluye el menú lateral que respeta los permisos del usuario.
?>
<main id="main" class="main">
    <!-- Sección de título de la página -->
    <div class="pagetitle">
        <h1>Registrar un nuevo transporte</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Transportes</li>
                <li class="breadcrumb-item active">Carga</li>
            </ol>
        </nav>
    </div>
    <!-- Contenido principal del formulario -->
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
                                <label for="marca_id" class="form-label">Marca (*)</label>
                                <select class="form-select" id="marca_id" name="marca_id" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    for ($i = 0; $i < $CantidadMarcas; $i++) {
                                        $Seleccionado = (!empty($MarcaSeleccionada) && $MarcaSeleccionada == $marcas[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $marcas[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $marcas[$i]['denominacion']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="modelo" class="form-label">Modelo (*)</label>
                                <input type="text" class="form-control" id="modelo" name="modelo" value="<?php echo !empty($ModeloValor) ? htmlspecialchars($ModeloValor) : ''; ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="anio" class="form-label">Año</label>
                                <input type="text" class="form-control" id="anio" name="anio" value="<?php echo !empty($AnioValor) ? htmlspecialchars($AnioValor) : ''; ?>" placeholder="AAAA">
                            </div>
                            <div class="col-6">
                                <label for="patente" class="form-label">Patente (*)</label>
                                <input type="text" class="form-control" id="patente" name="patente" value="<?php echo !empty($PatenteValor) ? htmlspecialchars($PatenteValor) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="disponible" name="disponible" <?php echo $DisponibleMarcado ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="disponible">
                                        Habilitado
                                    </label>
                                </div>
                            </div>
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
<?php require_once 'includes/footer.php'; // Se agrega el pie común para cerrar el HTML. ?>

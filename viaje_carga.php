<?php
/**
 * Formulario de alta de viajes.
 * Documentado línea a línea para comprender cómo se arma el viaje con chofer, transporte y destino.
 */

// Conexión a la base de datos y helpers compartidos.
require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php';
// Evitamos accesos no autenticados.
RequiereSesion();

// Los choferes no pueden crear viajes; si detectamos uno lo derivamos a su listado.
if (EsChofer()) {
    Redireccionar('viajes_listado.php');
}

// Se prepara la conexión a la base para consultar datos auxiliares.
$MiConexion = ConexionBD();

// Información utilizada por el template principal.
$pageTitle = 'Registrar un nuevo viaje';
$activePage = 'viaje_carga';

// Listas para poblar los combos del formulario.
$choferes = Listar_Choferes($MiConexion);
$transportes = Listar_Transportes($MiConexion);
$destinos = Listar_Destinos($MiConexion);
// Usuario actual (servirá para saber quién registró el viaje si fuese necesario).
$usuarioSesion = ObtenerUsuarioEnSesion();
$creadoPorId = 0;
if (isset($usuarioSesion['id'])) {
    $creadoPorId = $usuarioSesion['id'];
}

// Variables para controlar el mensaje de feedback al usuario.
$Mensaje = '';
$Estilo = 'warning';

// Al enviar el formulario se validan los datos y eventualmente se inserta el registro.
if (!empty($_POST['BotonRegistrar'])) {
    $Mensaje = Validar_Datos_Viaje($MiConexion);
    if (empty($Mensaje)) {
        if (Insertar_Viaje($MiConexion) != false) {
            $Mensaje = 'Se ha registrado correctamente.';
            $_POST = array();
            $Estilo = 'success';
        }
    }
}

// Totales usados para iterar las listas dentro del HTML.
$CantidadChoferes = count($choferes);
$CantidadTransportes = count($transportes);
$CantidadDestinos = count($destinos);

// Cabecera y navegación comunes del panel.
require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Registrar un nuevo viaje</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Viajes</li>
                <li class="breadcrumb-item active">Carga</li>
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
                                <label for="chofer_id" class="form-label">Chofer (*)</label>
                                <select class="form-select" id="chofer_id" name="chofer_id" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    // Se recuerda el chofer elegido en caso de errores de validación.
                                    $ChoferSeleccionado = !empty($_POST['chofer_id']) ? $_POST['chofer_id'] : '';
                                    for ($i = 0; $i < $CantidadChoferes; $i++) {
                                        $Seleccionado = (!empty($ChoferSeleccionado) && $ChoferSeleccionado == $choferes[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $choferes[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $choferes[$i]['apellido'] . ', ' . $choferes[$i]['nombre'] . ' - DNI ' . $choferes[$i]['dni']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="transporte_id" class="form-label">Transporte (*)</label>
                                <select class="form-select" id="transporte_id" name="transporte_id" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    $TransporteSeleccionado = !empty($_POST['transporte_id']) ? $_POST['transporte_id'] : '';
                                    for ($i = 0; $i < $CantidadTransportes; $i++) {
                                        $Seleccionado = (!empty($TransporteSeleccionado) && $TransporteSeleccionado == $transportes[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $transportes[$i]['id']; ?>" <?php echo $Seleccionado; ?>>
                                            <?php echo $transportes[$i]['marca'] . ' - ' . $transportes[$i]['modelo'] . ' - ' . $transportes[$i]['patente']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="fecha_programada" class="form-label">Fecha programada (*)</label>
                                <input type="text" class="form-control" id="fecha_programada" name="fecha_programada" placeholder="dd/mm/aaaa" value="<?php echo !empty($_POST['fecha_programada']) ? htmlspecialchars($_POST['fecha_programada']) : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="destino_id" class="form-label">Destino (*)</label>
                                <select class="form-select" id="destino_id" name="destino_id" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php
                                    $DestinoSeleccionado = !empty($_POST['destino_id']) ? $_POST['destino_id'] : '';
                                    for ($i = 0; $i < $CantidadDestinos; $i++) {
                                        $Seleccionado = (!empty($DestinoSeleccionado) && $DestinoSeleccionado == $destinos[$i]['id']) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $destinos[$i]['id']; ?>" <?php echo $Seleccionado; ?>><?php echo $destinos[$i]['denominacion']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="costo" class="form-label">Costo (*)</label>
                                <input type="text" class="form-control" id="costo" name="costo" value="<?php echo !empty($_POST['costo']) ? htmlspecialchars($_POST['costo']) : ''; ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="porcentaje_chofer" class="form-label">Porcentaje chofer (*)</label>
                                <input type="number" class="form-control" id="porcentaje_chofer" name="porcentaje_chofer" min="0" max="100" value="<?php echo !empty($_POST['porcentaje_chofer']) ? htmlspecialchars($_POST['porcentaje_chofer']) : ''; ?>" required>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-primary" type="submit" name="BotonRegistrar" value="Registrar">Registrar</button>
                                <a href="viaje_carga.php" class="btn btn-secondary">Limpiar Campos</a>
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
// Pie de página compartido del panel.
require_once 'includes/footer.php';
?>

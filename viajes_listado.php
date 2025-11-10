<?php
/**
 * Listado de viajes.
 * Aquí se muestra cómo se filtra la información según el nivel del usuario y se construye la tabla.
 */

// Dependencias necesarias para la base y los helpers.
require_once 'funciones/conexion.php';
require_once 'funciones/funciones.php';
// Solo usuarios autenticados pueden ingresar.
RequiereSesion();

// Conexión activa a la base de datos.
$MiConexion = ConexionBD();

// Variables que aprovecha el layout general del sitio.
$pageTitle = 'Listado de viajes registrados';
$activePage = 'viajes_listado';

// Datos del usuario actual para determinar permisos y filtros.
$usuarioActual = ObtenerUsuarioEnSesion();
$esChofer = isset($usuarioActual['id_nivel']) && (int) $usuarioActual['id_nivel'] === 3;
$choferFiltradoId = null;
if ($esChofer && isset($usuarioActual['id'])) {
    $choferFiltradoId = $usuarioActual['id'];
}
// Se obtienen los viajes, filtrando por chofer si corresponde.
$viajes = Listar_Viajes($MiConexion, $choferFiltradoId);

// Flags que deciden qué columnas se muestran en función del rol.
$mostrarCosto = true;
$mostrarMontoChofer = true;
$mostrarPorcentajeEnMonto = true;

if (!empty($usuarioActual['id_nivel'])) {
    $nivelActual = (int) $usuarioActual['id_nivel'];
    // Un chofer (nivel 3) no debería ver el costo del viaje ni el porcentaje aplicado.
    if ($nivelActual === 3) {
        $mostrarCosto = false;
        $mostrarPorcentajeEnMonto = false;
    }
    // Un supervisor (nivel 2) puede ver el costo pero no lo que cobra el chofer.
    if ($nivelActual === 2) {
        $mostrarMontoChofer = false;
    }
}

// Partes comunes del template.
require_once 'includes/header.php';
require_once 'includes/topbar.php';
require_once 'includes/sidebar.php';
?>
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Lista de viajes registrados</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Viajes</li>
                <li class="breadcrumb-item active">Listado</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Viajes cargados</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha viaje</th>
                                <th>Destino</th>
                                <th>Camión</th>
                                <th>Chofer</th>
                                <?php if ($mostrarCosto): ?>
                                    <th>Costo viaje</th>
                                <?php endif; ?>
                                <?php if ($mostrarMontoChofer): ?>
                                    <th>Monto Chofer</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$viajes): ?>
                                <tr>
                                    <td colspan="<?php echo 5 + (int) $mostrarCosto + (int) $mostrarMontoChofer; ?>" class="text-center">No hay viajes registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $CantidadViajes = count($viajes);
                                for ($i = 0; $i < $CantidadViajes; $i++) {
                                    // Se formatea la fecha para mostrarla en formato dd/mm/aaaa.
                                    $FechaFormateada = '';
                                    if (!empty($viajes[$i]['fecha_programada'])) {
                                        $Timestamp = strtotime($viajes[$i]['fecha_programada']);
                                        if ($Timestamp != false) {
                                            $FechaFormateada = date('d/m/Y', $Timestamp);
                                        }
                                    }
                                    // Se calcula cuánto cobra el chofer según el porcentaje definido.
                                    $MontoChofer = ((float) $viajes[$i]['costo'] * (int) $viajes[$i]['porcentaje_chofer']) / 100;
                                    ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td><?php echo $FechaFormateada; ?></td>
                                        <td><?php echo $viajes[$i]['destino']; ?></td>
                                        <td><?php echo $viajes[$i]['marca'] . ' - ' . $viajes[$i]['modelo'] . ' - ' . $viajes[$i]['patente']; ?></td>
                                        <td><?php echo $viajes[$i]['chofer_apellido'] . ', ' . $viajes[$i]['chofer_nombre']; ?></td>
                                        <?php if ($mostrarCosto): ?>
                                            <td>$ <?php echo number_format((float) $viajes[$i]['costo'], 2, ',', '.'); ?></td>
                                        <?php endif; ?>
                                        <?php if ($mostrarMontoChofer): ?>
                                            <td>
                                                $ <?php echo number_format($MontoChofer, 2, ',', '.'); ?>
                                                <?php if ($mostrarPorcentajeEnMonto): ?>
                                                    (<?php echo (int) $viajes[$i]['porcentaje_chofer']; ?>%)
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php } ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
// Footer compartido.
require_once 'includes/footer.php';
?>

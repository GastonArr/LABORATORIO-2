<?php
/**
 * Menú lateral del panel.
 * Según el rol del usuario se muestran diferentes secciones.
 */

// Página actual (se usa para marcar los enlaces activos).
$currentPage = '';
if (isset($activePage)) {
    $currentPage = $activePage;
}
// Se consulta el rol para decidir qué opciones habilitar.
$esAdminActual = EsAdministrador();
$esOperadorActual = EsOperador();

// Flags de visibilidad por sección.
$mostrarTransportes = $esAdminActual || $esOperadorActual;
$mostrarChoferes = $esAdminActual;
$mostrarCargaViajes = $esAdminActual || $esOperadorActual;

// Listas auxiliares utilizadas para conocer qué submenús deben aparecer expandidos.
$transportNavPages = array();
if ($mostrarTransportes) {
    $transportNavPages[] = 'camion_carga';
}
if ($mostrarChoferes) {
    $transportNavPages[] = 'choferes';
}

$viajesNavPages = array();
if ($mostrarCargaViajes) {
    $viajesNavPages[] = 'viaje_carga';
}
$viajesNavPages[] = 'viajes_listado';

$transportNavAbierto = in_array($currentPage, $transportNavPages, true);
$viajesNavAbierto = in_array($currentPage, $viajesNavPages, true);
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo $currentPage === 'dashboard' ? '' : 'collapsed'; ?>" href="index.php">
                <i class="bi bi-grid"></i>
                <span>Panel</span>
            </a>
        </li>

        <?php if ($mostrarTransportes || $mostrarChoferes): // Bloque de transportes (camiones y choferes). ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $transportNavAbierto ? '' : 'collapsed'; ?>" data-bs-target="#transportes-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-truck"></i><span>Transportes</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="transportes-nav" class="nav-content collapse <?php echo $transportNavAbierto ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                    <?php if ($mostrarTransportes): ?>
                        <li>
                            <a href="camion_carga.php" class="<?php echo $currentPage === 'camion_carga' ? 'active' : ''; ?>">
                                <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo transporte</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($mostrarChoferes): ?>
                        <li>
                            <a href="chofer_carga.php" class="<?php echo $currentPage === 'choferes' ? 'active' : ''; ?>">
                                <i class="bi bi-person-plus"></i><span>Cargar nuevo chofer</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link <?php echo $viajesNavAbierto ? '' : 'collapsed'; ?>" data-bs-target="#viajes-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-globe2"></i><span>Viajes</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="viajes-nav" class="nav-content collapse <?php echo $viajesNavAbierto ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
                <?php if ($mostrarCargaViajes): ?>
                    <li>
                        <a href="viaje_carga.php" class="<?php echo $currentPage === 'viaje_carga' ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-plus"></i><span>Cargar nuevo</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="viajes_listado.php" class="<?php echo $currentPage === 'viajes_listado' ? 'active' : ''; ?>">
                        <i class="bi bi-layout-text-window-reverse"></i><span>Listado de viajes</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</aside>

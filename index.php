<?php
/**
 * Sistema de Cargabilidad - ZOTGM
 * 
 * @author Kaori Andrea López Basurto
 * @collaborator Emerson Salvador Plancarte Cerecedo
 * @version 2025
 * @description Sistema de monitoreo de capacidad de carga (MW) para la Zona de Operación de Transmisión Guerrero Morelos
 * 
 * Características principales:
 * - Monitoreo de capacidad de carga en tiempo real
 * - Visualización semáforo con indicadores de estado
 * - Sistema de alertas y notificaciones
 * - Interfaz simplificada y eficiente
 * - Exportación de datos y reportes
 * - Dashboard con métricas de carga
 */

// OPTIMIZACIÓN 1: Headers de cache optimizados
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// OPTIMIZACIÓN 2: Generación optimizada de fechas
$dayone = date('Y-m-01');
$daypresent = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Cargabilidad de los Elementos</title>

    <!-- Bootstrap CSS y Icons desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="estilo.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Modal para inicio de sesión administrador -->
    <div class="modal fade" id="modalAdmin" tabindex="-1" aria-labelledby="modalAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdminLabel">Autenticación de Administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin-password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="admin-password"
                            placeholder="Ingresa la contraseña" />
                        <div id="admin-feedback" class="form-text text-danger d-none">Contraseña incorrecta.</div>
                        <div id="admin-success" class="form-text text-success d-none">¡Inicio de sesión exitoso!</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="confirm-admin" class="btn btn-primary">Entrar</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de carga -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-body d-flex align-items-center justify-content-center">
                    <div class="spinner-border text-light me-3" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <div>Cargando datos, por favor espere...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light position-relative">
        <div div class="container d-flex justify-content-between align-items-center"
            style="width: 90vw; max-width: 100%; margin: 0 auto; position: relative;">
            <a class="navbar-brand me-3" href="http://10.25.117.65:8086/zotgm/inicio.php">
                <img src="cfe_logo.png" alt="Logo" width="120" height="120" class="d-inline-block align-text-top" />
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido"
                aria-controls="navbarContenido" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="titulo-navbar"
                class="position-absolute top-50 start-50 translate-middle text-center fs-4 fw-bold text-wrap text-truncate"
                style="max-width: 70%; z-index: 1040;">
                Sistema de Evaluación de Cargabilidad de los <br>Elementos de la Red Eléctrica Guerrero-Morelos
            </div>

            <div class="collapse navbar-collapse" id="navbarContenido">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="http://10.25.117.67/icv">ICV de la ZOTGM</a>
                    </li>
                </ul>

                <!-- Contenedor botón admin: cambia según estado -->
                <div id="admin-button-container" class="ms-auto"></div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <div class="w-100" style="max-width: 90%;">
            <div class="mb-4">
                <form id="consultaForm" action="procesar.php" method="post">
                    <div class="row g-3 align-items-end">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">

                                <div class="flex-fill" style="min-width: 120px;">
                <label for="fechaInicio" class="form-label">Fecha Inicial</label>
                <input type="date" class="form-control w-100" id="fechaInicio" name="fechaInicio"
                    value="<?php echo $dayone ?>" required>
            </div>

                                <div class="flex-fill" style="min-width: 120px;">
                <label for="fechaFin" class="form-label">Fecha Final</label>
                <input type="date" class="form-control w-100" id="fechaFin" name="fechaFin"
                    value="<?php echo $daypresent ?>" required>
            </div>

                                <div class="flex-fill" style="min-width: 120px;">
                <label for="intervalo" class="form-label">Intervalo</label>
                <input type="number" class="form-control w-100" id="intervalo" name="intervalo" value="1" required>
            </div>

                                <div class="flex-fill" style="min-width: 120px;">
                <label for="unidad" class="form-label">Unidad</label>
                <select class="form-select w-100" id="unidad" name="unidad" required>
                    <option value="h">Horas</option>
                    <option value="m">Minutos</option>
                    <option value="s">Segundos</option>
                </select>
            </div>

                                <div class="flex-fill" style="min-width: 120px;">
                                    <button type="submit" class="btn btn-primary w-100">Consultar</button>
                                </div>

                                <div class="flex-fill" style="min-width: 120px;">
                                    <div class="dropdown">
                                        <button class="btn btn-info w-100 dropdown-toggle" type="button" id="exportarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-download me-1"></i>Exportar
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="exportarDropdown">
                                            <li><a class="dropdown-item" href="#" id="exportar-xls"><i class="bi bi-file-earmark-excel me-2"></i>Excel (.xls)</a></li>
                                            <li><a class="dropdown-item" href="#" id="exportar-csv"><i class="bi bi-file-earmark-text me-2"></i>CSV (.csv)</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <div id="resultadoTabla"></div>

            <!-- Área de filtros y búsqueda (se muestra cuando hay datos) -->
            <div id="filtros-area" class="mt-3" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros y Búsqueda</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="buscar-tag" class="form-label">Buscar Nodo</label>
                                <input type="text" class="form-control" id="buscar-tag" placeholder="Escriba para filtrar...">
                            </div>
                            <div class="col-md-3">
                                <label for="selector-elementos" class="form-label">Seleccionar Elementos</label>
                                <select class="form-select" id="selector-elementos" multiple size="4">
                                    <option value="">Cargando elementos...</option>
                                </select>
                                <small class="form-text text-muted">Ctrl+Click para selección múltiple</small>
                            </div>
                            <div class="col-md-2">
                                <label for="filtro-estado" class="form-label">Filtrar por Estado</label>
                                <select class="form-select" id="filtro-estado">
                                    <option value="">Todos los estados</option>
                                    <option value="success">Normal</option>
                                    <option value="warning">Precaución</option>
                                    <option value="danger">Crítico</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="ordenar-por" class="form-label">Ordenar por</label>
                                <select class="form-select" id="ordenar-por">
                                    <option value="tag">Nodo</option>
                                    <option value="normal">Tiempo Normal</option>
                                    <option value="precaucion">Tiempo Precaución</option>
                                    <option value="critico">Tiempo Crítico</option>
                                    <option value="total">Tiempo Total</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="w-100">
                                    <button type="button" class="btn btn-primary w-100 mb-1" id="aplicar-seleccion">
                                        <i class="bi bi-check-circle me-1"></i>Aplicar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary w-100" id="limpiar-filtros">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JS de Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SheetJS para exportación a Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <script src="autenticacion.js"></script>
    <script src="exportar.js"></script>
    <script src="tabla.js"></script>

    <!-- Script optimizado para enviar el formulario con fetch -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // OPTIMIZACIÓN 1: Cache de elementos DOM
            const form = document.getElementById('consultaForm');
            const resultadoTabla = document.getElementById('resultadoTabla');
            const loadingModal = document.getElementById('loadingModal');
            const modalInstance = new bootstrap.Modal(loadingModal, {
                backdrop: 'static',
                keyboard: false
            });

            // OPTIMIZACIÓN 2: Inicialización optimizada de tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = Array.from(tooltipTriggerList).map(tooltipTriggerEl => 
                new bootstrap.Tooltip(tooltipTriggerEl)
            );

            // OPTIMIZACIÓN 3: Evento de formulario optimizado
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                modalInstance.show();

            const formData = new FormData(this);

            try {
                const response = await fetch('procesar.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.text();
                    resultadoTabla.innerHTML = data;

                    // Inicializar filtros después de cargar datos
                    if ($('#resultadoTabla table').length > 0) {
                        // Mostrar área de filtros
                        $('#filtros-area').show();
                        
                        // Inicializar funcionalidad de tabla
                        setTimeout(() => {
                            if (typeof inicializarFiltros === 'function') {
                                inicializarFiltros();
                            } else {
                                console.log('Función inicializarFiltros no encontrada');
                            }
                        }, 200);
                    }

            } catch (error) {
                alert("Error al cargar datos.");
            } finally {
                    // OPTIMIZACIÓN 4: Manejo optimizado del cierre del modal
                    if (loadingModal.classList.contains('show')) {
                        modalInstance.hide();
                } else {
                        loadingModal.addEventListener('shown.bs.modal', () => {
                            modalInstance.hide();
                    }, { once: true });
                }
            }
            });
        });
    </script>
</body>

</html>
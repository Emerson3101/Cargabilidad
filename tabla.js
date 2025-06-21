/**
 * Sistema de Procesamiento de Datos de Cargabilidad - ZOTGM
 * 
 * @author Kaori Andrea López Basurto
 * @collaborator Emerson Salvador Plancarte Cerecedo
 * @version 2025
 * @description Manejo de datos, visualización semáforo y funcionalidades del sistema de cargabilidad
 * 
 * Funcionalidades:
 * - Carga y procesamiento de datos de capacidad de carga
 * - Visualización semáforo con indicadores de estado
 * - Sistema de alertas y notificaciones en tiempo real
 * - Exportación de datos y reportes
 * - Interfaz optimizada para monitoreo continuo
 * - Dashboard con métricas de carga
 */

// FUNCIONALIDADES DE TABLA COMPACTA Y FILTROS PARA CARGABILIDAD
$(document).ready(function() {
    let datosOriginales = [];
    let datosFiltrados = [];

    // Mostrar filtros cuando se cargan datos
    $(document).on('DOMContentLoaded', function() {
        if ($('#resultadoTabla table').length > 0) {
            inicializarFiltros();
        }
    });

    // Inicializar filtros cuando se cargan datos - FUNCIÓN GLOBAL
    window.inicializarFiltros = function() {
        $('#filtros-area').show();
        cargarDatosTabla();
        configurarEventos();
        // Cargar selector después de que los datos estén disponibles
        setTimeout(() => {
            cargarSelectorElementos();
        }, 100);
    };

    // Cargar datos de la tabla
    function cargarDatosTabla() {
        datosOriginales = [];
        $('#resultadoTabla tbody tr').each(function() {
            const $fila = $(this);
            const tag = $fila.find('td:first').text().trim();
            const tiempoNormal = extraerTiempo($fila.find('td:nth-child(2) .fw-bold').text());
            const tiempoPrecaucion = extraerTiempo($fila.find('td:nth-child(3) .fw-bold').text());
            const tiempoCritico = extraerTiempo($fila.find('td:nth-child(4) .fw-bold').text());
            const estado = determinarEstado($fila);
            
            datosOriginales.push({
                elemento: $fila,
                tag: tag,
                tiempoNormal: tiempoNormal,
                tiempoPrecaucion: tiempoPrecaucion,
                tiempoCritico: tiempoCritico,
                tiempoTotal: tiempoNormal + tiempoPrecaucion + tiempoCritico,
                estado: estado
            });
        });
        datosFiltrados = [...datosOriginales];
    }

    // Extraer tiempo en segundos del texto formateado
    function extraerTiempo(texto) {
        const match = texto.match(/(\d+) h (\d+) m (\d+) s/);
        if (match) {
            return parseInt(match[1]) * 3600 + parseInt(match[2]) * 60 + parseInt(match[3]);
        }
        return 0;
    }

    // Determinar estado de la fila
    function determinarEstado($fila) {
        if ($fila.hasClass('table-danger')) return 'danger';
        if ($fila.hasClass('table-warning')) return 'warning';
        return 'success';
    }

    // Configurar eventos de filtros
    function configurarEventos() {
        // Búsqueda por tag
        $('#buscar-tag').on('input', function() {
            aplicarFiltros();
        });

        // Filtro por estado
        $('#filtro-estado').on('change', function() {
            aplicarFiltros();
        });

        // Ordenamiento
        $('#ordenar-por').on('change', function() {
            aplicarFiltros();
        });

        // Aplicar selección de elementos
        $('#aplicar-seleccion').click(function() {
            aplicarFiltros();
        });

        // Limpiar filtros
        $('#limpiar-filtros').click(function() {
            $('#buscar-tag').val('');
            $('#filtro-estado').val('');
            $('#ordenar-por').val('tag');
            $('#selector-elementos').val('todos');
            aplicarFiltros();
        });
    }

    // Aplicar filtros y ordenamiento
    function aplicarFiltros() {
        const busqueda = $('#buscar-tag').val().toLowerCase();
        const estadoFiltro = $('#filtro-estado').val();
        const ordenamiento = $('#ordenar-por').val();
        const elementosSeleccionados = $('#selector-elementos').val();

        // Filtrar datos
        datosFiltrados = datosOriginales.filter(item => {
            const coincideBusqueda = item.tag.toLowerCase().includes(busqueda);
            const coincideEstado = !estadoFiltro || item.estado === estadoFiltro;
            
            // Filtro por elementos seleccionados
            let coincideElemento = true;
            if (elementosSeleccionados && elementosSeleccionados.length > 0) {
                if (elementosSeleccionados.includes('todos')) {
                    coincideElemento = true;
                } else {
                    coincideElemento = elementosSeleccionados.includes(item.tag);
                }
            }
            
            return coincideBusqueda && coincideEstado && coincideElemento;
        });

        // Ordenar datos
        datosFiltrados.sort((a, b) => {
            switch (ordenamiento) {
                case 'tag':
                    return a.tag.localeCompare(b.tag);
                case 'normal':
                    return b.tiempoNormal - a.tiempoNormal;
                case 'precaucion':
                    return b.tiempoPrecaucion - a.tiempoPrecaucion;
                case 'critico':
                    return b.tiempoCritico - a.tiempoCritico;
                case 'total':
                    return b.tiempoTotal - a.tiempoTotal;
                default:
                    return 0;
            }
        });

        // Actualizar tabla
        actualizarTabla();
    }

    // Actualizar tabla con datos filtrados
    function actualizarTabla() {
        const $tbody = $('#resultadoTabla tbody');
        $tbody.empty();

        if (datosFiltrados.length === 0) {
            $tbody.append('<tr><td colspan="5" class="text-center text-muted">No se encontraron resultados</td></tr>');
            return;
        }

        datosFiltrados.forEach(item => {
            $tbody.append(item.elemento.clone());
        });

        // Actualizar contador
        actualizarContador();
    }

    // Actualizar contador de resultados
    function actualizarContador() {
        const total = datosOriginales.length;
        const filtrados = datosFiltrados.length;
        
        let contador = $(`<small class="text-muted">Mostrando ${filtrados} de ${total} tags</small>`);
        
        if ($('#contador-resultados').length === 0) {
            $('#resultadoTabla .card-header h5').append(' <span id="contador-resultados"></span>');
        }
        
        $('#contador-resultados').html(contador);
    }

    // Función para exportar solo datos filtrados
    window.exportarFiltrados = function() {
        if (datosFiltrados.length === 0) {
            alert('No hay datos filtrados para exportar.');
            return;
        }
        
        // Crear datos para exportación
        const datos = [];
        datos.push(['Sistema de Evaluación de Cargabilidad de los Elementos de la Red Eléctrica Guerrero-Morelos']);
        datos.push([]);
        datos.push(['Fecha de Exportación:', new Date().toLocaleString('es-MX')]);
        datos.push(['Filtros aplicados:', obtenerFiltrosAplicados()]);
        datos.push([]);
        datos.push(['Tag', 'Tiempo Normal', 'Tiempo Precaución', 'Tiempo Crítico', 'Tiempo Total', 'Estado']);
        
        datosFiltrados.forEach(item => {
            datos.push([
                item.tag,
                formatearTiempo(item.tiempoNormal),
                formatearTiempo(item.tiempoPrecaucion),
                formatearTiempo(item.tiempoCritico),
                formatearTiempo(item.tiempoTotal),
                item.estado === 'danger' ? 'Crítico' : item.estado === 'warning' ? 'Precaución' : 'Normal'
            ]);
        });

        return datos;
    };

    // Obtener descripción de filtros aplicados
    function obtenerFiltrosAplicados() {
        const filtros = [];
        const busqueda = $('#buscar-tag').val();
        const estado = $('#filtro-estado').val();
        const elementosSeleccionados = $('#selector-elementos').val();
        
        if (busqueda) filtros.push(`Búsqueda: "${busqueda}"`);
        if (estado) {
            const estadoTexto = estado === 'danger' ? 'Crítico' : estado === 'warning' ? 'Precaución' : 'Normal';
            filtros.push(`Estado: ${estadoTexto}`);
        }
        if (elementosSeleccionados && elementosSeleccionados.length > 0) {
            if (elementosSeleccionados.includes('todos')) {
                filtros.push('Elementos: Todos');
            } else {
                const elementosTexto = elementosSeleccionados.join(', ');
                filtros.push(`Elementos: ${elementosTexto}`);
            }
        }
        
        return filtros.length > 0 ? filtros.join(', ') : 'Ninguno';
    }

    // Función para formatear tiempo
    function formatearTiempo(segundos) {
        const h = Math.floor(segundos / 3600);
        const m = Math.floor((segundos % 3600) / 60);
        const s = segundos % 60;
        return `${h} h ${m} m ${s} s`;
    }

    // Cargar selector de elementos
    function cargarSelectorElementos() {
        const $selector = $('#selector-elementos');
        $selector.empty();
        
        // Agregar opción para mostrar todos
        $selector.append('<option value="todos">Mostrar todos los elementos</option>');
        
        // Agregar cada elemento único
        if (datosOriginales && datosOriginales.length > 0) {
            const elementosUnicos = [...new Set(datosOriginales.map(item => item.tag))];
            elementosUnicos.sort().forEach(elemento => {
                $selector.append(`<option value="${elemento}">${elemento}</option>`);
            });
        }
        
        // Seleccionar "mostrar todos" por defecto
        $selector.val('todos');
    }
}); 
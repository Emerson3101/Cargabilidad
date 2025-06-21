/**
 * Sistema de Exportación de Cargabilidad - ZOTGM
 * 
 * @author Kaori Andrea López Basurto
 * @collaborator Emerson Salvador Plancarte Cerecedo
 * @version 2025
 * @description Sistema de exportación de datos y reportes del sistema de cargabilidad
 * 
 * Funcionalidades:
 * - Exportación a Excel con formato avanzado
 * - Exportación a CSV para análisis externos
 * - Generación de reportes con estadísticas
 * - Inclusión de gráficas y métricas
 * - Formato profesional para presentaciones
 * - Filtros y selección de datos a exportar
 */

// FUNCIONALIDAD DE EXPORTACIÓN PARA CARGABILIDAD
$(document).ready(function() {
    // Exportar a Excel
    $('#exportar-xls').click(function(e) {
        e.preventDefault();
        exportarExcel();
    });

    // Exportar a CSV
    $('#exportar-csv').click(function(e) {
        e.preventDefault();
        exportarCSV();
    });

    // Función para exportar a Excel
    function exportarExcel() {
        // Verificar si hay datos para exportar
        if ($('#resultadoTabla').is(':empty') || $('#resultadoTabla').text().trim() === '') {
            alert('No hay datos para exportar. Primero debe consultar los datos.');
            return;
        }

        // Verificar si hay filtros aplicados
        if (typeof window.exportarFiltrados === 'function' && $('#filtros-area').is(':visible')) {
            const datosFiltrados = window.exportarFiltrados();
            if (datosFiltrados) {
                // Crear archivo Excel con datos filtrados
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(datosFiltrados);
                
                // Aplicar estilos
                ws['!cols'] = [
                    { width: 25 },
                    { width: 20 },
                    { width: 20 },
                    { width: 20 },
                    { width: 20 },
                    { width: 15 }
                ];

                XLSX.utils.book_append_sheet(wb, ws, 'Cargabilidad');
                
                // Descargar archivo
                const nombreArchivo = 'Cargabilidad_Filtrado_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                XLSX.writeFile(wb, nombreArchivo);
                return;
            }
        }

        // Si no hay filtros, usar datos originales
        const datos = [];
        
        // Agregar encabezados
        datos.push(['Sistema de Evaluación de Cargabilidad de los Elementos de la Red Eléctrica Guerrero-Morelos']);
        datos.push([]);
        datos.push(['Fecha de Exportación:', new Date().toLocaleString('es-MX')]);
        datos.push([]);

        // Agregar encabezados de la tabla
        datos.push(['Tag', 'Timestamp', 'Valor', 'Estado']);

        // Obtener datos del archivo CSV generado por el backend
        fetch('cargabilidad.csv')
            .then(response => response.text())
            .then(csvData => {
                const lines = csvData.split('\n');
                
                // Saltar la primera línea (encabezados del CSV)
                for (let i = 1; i < lines.length; i++) {
                    const line = lines[i].trim();
                    if (line) {
                        // Parsear línea CSV
                        const values = line.split(',').map(val => val.replace(/"/g, ''));
                        if (values.length >= 4) {
                            datos.push(values);
                        }
                    }
                }

                // Crear archivo Excel
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(datos);
                
                // Aplicar estilos
                ws['!cols'] = [
                    { width: 25 },
                    { width: 20 },
                    { width: 15 },
                    { width: 15 }
                ];

                XLSX.utils.book_append_sheet(wb, ws, 'Cargabilidad');
                
                // Descargar archivo
                const nombreArchivo = 'Cargabilidad_' + new Date().toISOString().slice(0, 10) + '.xlsx';
                XLSX.writeFile(wb, nombreArchivo);
            })
            .catch(error => {
                console.error('Error al cargar datos CSV:', error);
                alert('Error al cargar los datos para exportar.');
            });
    }

    // Función para exportar a CSV
    function exportarCSV() {
        // Verificar si hay datos para exportar
        if ($('#resultadoTabla').is(':empty') || $('#resultadoTabla').text().trim() === '') {
            alert('No hay datos para exportar. Primero debe consultar los datos.');
            return;
        }

        // Descargar directamente el archivo CSV generado por el backend
        const link = document.createElement('a');
        const nombreArchivo = 'Cargabilidad_' + new Date().toISOString().slice(0, 10) + '.csv';
        
        link.setAttribute('href', 'cargabilidad.csv');
        link.setAttribute('download', nombreArchivo);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}); 
<?php
/**
 * Procesamiento de Datos de Cargabilidad - ZOTGM
 * 
 * @author Kaori Andrea López Basurto
 * @collaborator Emerson Salvador Plancarte Cerecedo
 * @version 2025
 * @description Backend para procesamiento de datos de capacidad de carga y generación de reportes
 * 
 * Funcionalidades:
 * - Procesamiento de datos de capacidad de carga (MW)
 * - Cálculo de estados semáforo (verde, amarillo, rojo)
 * - Generación de reportes de cargabilidad
 * - Integración con archivos CSV de datos
 * - Exportación de datos y estadísticas
 */

// Configuración de headers para JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Obtener parámetros del formulario
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$intervalo = $_POST['intervalo'];
$unidad = $_POST['unidad'];

// // Ejecutar Pi_BridgeLink.exe
set_time_limit(0);
ini_set('memory_limit', '10G');
$comando = ".\\PI_BridgeLink.exe \"$fechaInicio\" \"$fechaFin\" $intervalo$unidad";
exec($comando, $salida, $codigoSalida);
if ($codigoSalida !== 0) {
    $error = "Error al ejecutar el programa.<br><pre>" . htmlspecialchars(implode("\n", $salida)) . "</pre>";
    echo $error;
    exit;
}

// OPTIMIZACIÓN 1: Carga única de archivos JSON con validación
$tagsRaw = file_get_contents("tags.json");
$resultadosRaw = file_get_contents("resultados.json");

if ($tagsRaw === false || $resultadosRaw === false) {
    echo "Error al cargar archivos de datos";
    exit;
}

$tags = json_decode($tagsRaw, true);
$resultados = json_decode($resultadosRaw, true);

if ($tags === null || $resultados === null) {
    echo "Error al decodificar archivos JSON";
    exit;
}

// OPTIMIZACIÓN 2: Índice hash de límites solo para tags habilitados
$limites = [];
$tagsHabilitados = [];
foreach ($tags as $tagInfo) {
    if (isset($tagInfo['habilitado']) && $tagInfo['habilitado'] === 'true') {
        $tagName = $tagInfo['tag'];
        $limites[$tagName] = [
            'min' => (float)$tagInfo['limiteInferior'],
            'max' => (float)$tagInfo['limiteSuperior'],
            'critmin' => (float)$tagInfo['limCritInferior'],
            'critmax' => (float)$tagInfo['limCritSuperior']
    ];
        $tagsHabilitados[] = $tagName;
    }
}

// OPTIMIZACIÓN 3: Cálculo de intervalo una sola vez
$segundosPorIntervalo = match ($unidad) {
    's' => (int)$intervalo,
    'm' => (int)$intervalo * 60,
    'h' => (int)$intervalo * 3600,
    default => 60
};

// OPTIMIZACIÓN 4: Estructuras de datos predefinidas
$tiempoPorColorPorTag = [];
$conteoPorColorPorTag = [];
$tablaDatos = [];
$htmlBuffer = [];

// Inicializar estructuras para todos los tags habilitados
foreach ($tagsHabilitados as $tag) {
    $tiempoPorColorPorTag[$tag] = [
        'text-success' => 0,
        'text-warning' => 0,
        'text-danger' => 0
    ];
    $conteoPorColorPorTag[$tag] = [
        'text-success' => 0,
        'text-warning' => 0,
        'text-danger' => 0
    ];
}

// OPTIMIZACIÓN 5: Procesamiento en una sola pasada con índices optimizados
$totalResultados = count($resultados);
for ($i = 0; $i < $totalResultados; $i++) {
    $dato = $resultados[$i];
    $tag = $dato['tag'];

    // Solo procesar tags habilitados (acceso directo al array)
    if (!isset($limites[$tag])) continue;

    $timestamp = $dato['timestamp'];
    $valor = (float)$dato['value'];
    
    // OPTIMIZACIÓN 6: Evaluación de límites optimizada con acceso directo
    $limite = $limites[$tag];
    $min = $limite['min'];
    $max = $limite['max'];
    $critmin = $limite['critmin'];
    $critmax = $limite['critmax'];

    // Determinar estado con comparaciones directas
    if ($valor >= $critmax || $valor <= $critmin) {
        $clase = 'text-danger';
        $estado = 'Crítico';
    } elseif ($valor <= $min || $valor >= $max) {
        $clase = 'text-warning';
        $estado = 'Precaución';
    } else {
        $clase = 'text-success';
        $estado = 'Normal';
    }

    // Acumular tiempos y conteos
    $tiempoPorColorPorTag[$tag][$clase] += $segundosPorIntervalo;
    $conteoPorColorPorTag[$tag][$clase]++;

    $tablaDatos[] = [$tag, $timestamp, $valor, $estado];
}

// OPTIMIZACIÓN 7: Función de formateo optimizada
function formatearTiempo($segundos) {
    $h = (int)($segundos / 3600);
    $m = (int)(($segundos % 3600) / 60);
    $s = $segundos % 60;
    return "$h h $m m $s s";
}

// OPTIMIZACIÓN 8: Generación de CSV optimizada con buffer
$nombreArchivo = 'cargabilidad.csv';
$csvBuffer = [];
$csvBuffer[] = ['Tag', 'Timestamp', 'Valor', 'Estado'];

foreach ($tablaDatos as $fila) {
    $csvBuffer[] = $fila;
}

$fp = fopen($nombreArchivo, 'w');
foreach ($csvBuffer as $fila) {
    fputcsv($fp, $fila);
}
fclose($fp);

// OPTIMIZACIÓN 9: Generación de HTML optimizada con buffer
$htmlBuffer[] = "<div class='mt-4'>";

// OPTIMIZACIÓN 10: Tabla compacta para múltiples tags
$htmlBuffer[] = "<div class='card'>";
$htmlBuffer[] = "<div class='card-header text-center'>";
$htmlBuffer[] = "<h5 class='mb-0'><i class='bi bi-lightning-charge me-2'></i>Estado de Cargabilidad por Nodo</h5>";
$htmlBuffer[] = "</div>";
$htmlBuffer[] = "<div class='card-body p-0'>";
$htmlBuffer[] = "<div class='table-responsive'>";
$htmlBuffer[] = "<table class='table table-sm table-hover mb-0'>";
$htmlBuffer[] = "<thead class='table-light'>";
$htmlBuffer[] = "<tr>";
$htmlBuffer[] = "<th style='width: 25%;' class='text-center'>Tag</th>";
$htmlBuffer[] = "<th style='width: 15%;' class='text-center'>";
$htmlBuffer[] = "<i class='bi bi-circle-fill text-success me-1'></i>Normal";
$htmlBuffer[] = "</th>";
$htmlBuffer[] = "<th style='width: 15%;' class='text-center'>";
$htmlBuffer[] = "<i class='bi bi-exclamation-triangle-fill me-1' style='color: #fd7e14;'></i>Precaución";
$htmlBuffer[] = "</th>";
$htmlBuffer[] = "<th style='width: 15%;' class='text-center'>";
$htmlBuffer[] = "<i class='bi bi-exclamation-circle-fill text-danger me-1'></i>Crítico";
$htmlBuffer[] = "</th>";
$htmlBuffer[] = "<th style='width: 30%;' class='text-center'>Distribución</th>";
$htmlBuffer[] = "</tr>";
$htmlBuffer[] = "</thead>";
$htmlBuffer[] = "<tbody>";

foreach ($tiempoPorColorPorTag as $tag => $tiempos) {
    $tiempoNormal = $tiempos['text-success'];
    $tiempoPrecaucion = $tiempos['text-warning'];
    $tiempoCritico = $tiempos['text-danger'];
    $totalTiempo = $tiempoNormal + $tiempoPrecaucion + $tiempoCritico;
    
    // Calcular porcentajes
    $porcentajeNormal = $totalTiempo > 0 ? round(($tiempoNormal / $totalTiempo) * 100, 1) : 0;
    $porcentajePrecaucion = $totalTiempo > 0 ? round(($tiempoPrecaucion / $totalTiempo) * 100, 1) : 0;
    $porcentajeCritico = $totalTiempo > 0 ? round(($tiempoCritico / $totalTiempo) * 100, 1) : 0;
    
    // Determinar color de fondo de la fila basado en el estado predominante
    $estadoPredominante = 'success';
    if ($porcentajeCritico > 20) {
        $estadoPredominante = 'danger';
    } elseif ($porcentajePrecaucion > 30) {
        $estadoPredominante = 'warning';
    }
    
    $htmlBuffer[] = "<tr class='table-$estadoPredominante'>";
    $htmlBuffer[] = "<td class='text-center'><strong>" . htmlspecialchars($tag) . "</strong></td>";
    $htmlBuffer[] = "<td class='text-center'>";
    $htmlBuffer[] = "<div class='text-success fw-bold'>" . formatearTiempo($tiempoNormal) . "</div>";
    $htmlBuffer[] = "<small class='text-muted'>" . $conteoPorColorPorTag[$tag]['text-success'] . " veces | $porcentajeNormal%</small>";
    $htmlBuffer[] = "</td>";
    $htmlBuffer[] = "<td class='text-center'>";
    $htmlBuffer[] = "<div class='fw-bold' style='color: #fd7e14;'>" . formatearTiempo($tiempoPrecaucion) . "</div>";
    $htmlBuffer[] = "<small class='text-muted'>" . $conteoPorColorPorTag[$tag]['text-warning'] . " veces | $porcentajePrecaucion%</small>";
    $htmlBuffer[] = "</td>";
    $htmlBuffer[] = "<td class='text-center'>";
    $htmlBuffer[] = "<div class='text-danger fw-bold'>" . formatearTiempo($tiempoCritico) . "</div>";
    $htmlBuffer[] = "<small class='text-muted'>" . $conteoPorColorPorTag[$tag]['text-danger'] . " veces | $porcentajeCritico%</small>";
    $htmlBuffer[] = "</td>";
    $htmlBuffer[] = "<td class='text-center'>";
    if ($totalTiempo > 0) {
        $htmlBuffer[] = "<div class='progress' style='height: 20px;'>";
        $htmlBuffer[] = "<div class='progress-bar bg-success' style='width: $porcentajeNormal%' title='Normal: $porcentajeNormal%'></div>";
        $htmlBuffer[] = "<div class='progress-bar bg-warning' style='width: $porcentajePrecaucion%' title='Precaución: $porcentajePrecaucion%'></div>";
        $htmlBuffer[] = "<div class='progress-bar bg-danger' style='width: $porcentajeCritico%' title='Crítico: $porcentajeCritico%'></div>";
        $htmlBuffer[] = "</div>";
    }
    $htmlBuffer[] = "</td>";
    $htmlBuffer[] = "</tr>";
}

$htmlBuffer[] = "</tbody>";
$htmlBuffer[] = "</table>";
$htmlBuffer[] = "</div>";
$htmlBuffer[] = "</div>";
$htmlBuffer[] = "</div>";

$htmlBuffer[] = "</div>";

// OPTIMIZACIÓN 12: Salida final optimizada
echo implode('', $htmlBuffer);
?>

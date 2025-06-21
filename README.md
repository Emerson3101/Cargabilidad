# Sistema de Cargabilidad - ZOTGM

## 📋 Descripción

Sistema de monitoreo y análisis de cargabilidad de la red eléctrica para la Zona de Operación de Transmisión Guerrero Morelos (ZOTGM). Este sistema permite el seguimiento de la capacidad de carga (MW) de diferentes elementos de la red, proporcionando una visualización clara del estado operativo mediante un sistema de semáforos.

**⚠️ Importante:** Este sistema está diseñado para funcionar en conjunto con archivos .exe específicos que recopilan datos del sistema PI (Plant Information System). El archivo .exe debe generar un archivo `resultados.json` con el formato especificado.

## 🚀 Características Principales

- **Monitoreo en tiempo real** de capacidad de carga (MW)
- **Sistema de semáforos** para visualización rápida del estado
- **Interfaz de tarjetas** con barras de progreso
- **Exportación de datos** a Excel y CSV
- **Sistema de autenticación** para administradores
- **Gestión de tags** y configuración de límites
- **Procesamiento optimizado** de grandes volúmenes de datos

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript (jQuery)
- **Base de datos:** Archivos JSON
- **Servidor:** Apache (XAMPP)
- **Sistema PI:** Plant Information System (OSIsoft)
- **Extractor de datos:** PI_BridgeLink.exe (personalizado)

## 📁 Estructura del Proyecto

```
Cargabilidad/
├── index.php              # Página principal del sistema
├── procesar.php           # Backend para procesamiento de datos
├── auth.php               # Sistema de autenticación
├── tagadmin.php           # Administración de tags
├── tabla.js               # Lógica de la tabla y semáforos
├── exportar.js            # Funciones de exportación
├── autenticacion.js       # Autenticación frontend
├── estilo.css             # Estilos del sistema
├── tags.json              # Configuración de tags y límites
├── PI_BridgeLink.exe      # Programa de extracción de datos PI
├── resultados.json        # Datos extraídos del sistema PI
├── cargabilidad.csv       # Datos de cargabilidad
└── README.md              # Este archivo
```

## 🔌 Integración con Sistema PI

### Requisitos del Archivo .exe
El sistema requiere un archivo ejecutable (ej: `PI_BridgeLink.exe`) que:

1. **Se conecte al sistema PI** usando las credenciales configuradas
2. **Reciba parámetros de fecha** (fecha_inicial, fecha_final)
3. **Extraiga datos de carga** de los tags configurados
4. **Genere un archivo `resultados.json`** con el formato especificado

### Formato del Archivo resultados.json

```json
[
  {
    "tag": "02GMOQMD B -01    CARGA         MW",
    "timestamp": "2025-06-21 10:30:15",
    "value": 185.6
  },
  {
    "tag": "02GMOPIC B -01    CARGA         MW", 
    "timestamp": "2025-06-21 10:30:20",
    "value": 142.3
  },
  {
    "tag": "02GMOQMD B -02    CARGA         MW",
    "timestamp": "2025-06-21 10:30:25", 
    "value": 198.7
  },
  {
    "tag": "02GMOLAT B -01    CARGA         MW",
    "timestamp": "2025-06-21 10:30:30",
    "value": 165.2
  }
]
```

### Estructura de Datos
- **tag:** Identificador del punto de medición (debe coincidir con tags.json)
- **timestamp:** Fecha y hora de la medición (formato: YYYY-MM-DD HH:MM:SS)
- **value:** Valor de carga medido en MW (número decimal)

## 🎯 Funcionalidades

### Sistema de Semáforos
- **Verde:** Carga normal (0-80% de capacidad)
- **Amarillo:** Carga moderada (80-95% de capacidad)
- **Rojo:** Carga crítica (95-100% de capacidad)
- **Gris:** Sin datos o fuera de servicio

### Visualización de Datos
- **Tarjetas individuales** para cada elemento
- **Barras de progreso** con porcentajes
- **Colores dinámicos** según el estado
- **Información detallada** al hacer hover

### Procesamiento de Datos
- Extracción automática de datos con PI_BridgeLink.exe
- Cálculo de porcentajes de capacidad
- Clasificación automática por estado
- Optimización de memoria para grandes volúmenes

### Exportación
- Exportación a Excel con formato profesional
- Exportación a CSV para análisis externos
- Reportes detallados por estado
- Estadísticas de cargabilidad

## 🔧 Instalación

1. **Requisitos:**
   - XAMPP (Apache + PHP 7.4+)
   - Git
   - Acceso al sistema PI
   - Archivo .exe de extracción de datos

2. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Emerson3101/Cargabilidad.git
   ```

3. **Configurar en XAMPP:**
   - Copiar la carpeta `Cargabilidad` a `htdocs/`
   - Acceder via `http://localhost/Cargabilidad/`

4. **Configuración inicial:**
   - Verificar permisos de escritura en archivos JSON
   - Configurar PI_BridgeLink.exe con credenciales PI
   - Verificar que el archivo .exe genere resultados.json

## 📊 Uso del Sistema

### Acceso Principal
- URL: `http://localhost/Cargabilidad/`
- Interfaz principal con sistema de semáforos
- Visualización por tarjetas con barras de progreso

### Procesamiento de Datos
1. Seleccionar fechas de inicio y fin
2. Hacer clic en "Procesar Datos"
3. El sistema ejecutará PI_BridgeLink.exe automáticamente
4. Los resultados se mostrarán en tarjetas con semáforos

### Interpretación de Semáforos
- **🟢 Verde:** Operación normal, carga dentro de límites seguros
- **🟡 Amarillo:** Atención requerida, carga aproximándose a límites
- **🔴 Rojo:** Alerta crítica, carga cerca del máximo
- **⚫ Gris:** Sin datos disponibles

### Exportación
1. Usar botones "Exportar a Excel" o "Exportar a CSV"
2. Los archivos se descargan automáticamente
3. Incluyen todos los datos y estados de cargabilidad

## 🔐 Autenticación

- **Contraseña por defecto:** `123`
- **Acceso:** Botón "Admin" en la barra de navegación
- **Funciones:** Administración de tags y configuración

## 📈 Métricas del Sistema

- **Total de Elementos:** Número total de puntos monitoreados
- **Elementos Verdes:** Operando normalmente
- **Elementos Amarillos:** Requieren atención
- **Elementos Rojos:** Críticos
- **Elementos Sin Datos:** Sin información disponible

## 🎨 Características de Diseño

- **Responsive:** Adaptable a diferentes tamaños de pantalla
- **Intuitivo:** Sistema de semáforos fácil de interpretar
- **Optimizado:** Carga rápida con procesamiento eficiente
- **Profesional:** Diseño corporativo con logo CFE

## 🔄 Actualizaciones

### Versión 2025
- Sistema de semáforos con tarjetas
- Barras de progreso visuales
- Exportación mejorada
- Optimización de memoria
- Interfaz moderna y responsiva

## 👨‍💻 Autor

**Emerson Salvador Plancarte Cerecedo**
- Email: emersonsalvador07@hotmail.com
- GitHub: [Emerson3101](https://github.com/Emerson3101)

## 📄 Licencia

Este proyecto es propiedad de la Comisión Federal de Electricidad (CFE) y está destinado para uso interno de la ZOTGM.

---

**Zona de Operación de Transmisión Guerrero Morelos (ZOTGM)**
*Sistema de Monitoreo de Cargabilidad* 
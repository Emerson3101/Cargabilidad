# Sistema de Cargabilidad (SCRE) - ZOTGM

## 📋 Descripción

Sistema de monitoreo de cargabilidad para la Zona de Operación de Transmisión Guerrero Morelos (ZOTGM). Este sistema permite el análisis de la capacidad de carga de la red eléctrica, monitoreo de parámetros de potencia y gestión de eventos de cargabilidad.

## 🚀 Características Principales

- **Monitoreo en tiempo real** de parámetros de cargabilidad
- **Análisis automático** de eventos de sobrecarga
- **Cálculo de índices** de cargabilidad por nodo
- **Sistema de semáforos** visual para estado de carga
- **Exportación de datos** a Excel y CSV
- **Sistema de autenticación** para administradores
- **Interfaz intuitiva** con diseño de tarjetas

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript (jQuery)
- **Base de datos:** Archivos JSON
- **Servidor:** Apache (XAMPP)

## 📁 Estructura del Proyecto

```
Cargabilidad/
├── index.php              # Página principal del sistema
├── procesar.php           # Backend para procesamiento de datos
├── auth.php               # Sistema de autenticación
├── tagadmin.php           # Administración de tags
├── tabla.js               # Gestión de tabla de datos
├── exportar.js            # Sistema de exportación
├── autenticacion.js       # Autenticación frontend
├── estilo.css             # Estilos del sistema
├── tags.json              # Configuración de tags y límites
├── eval.json              # Evaluaciones guardadas
├── PI_BridgeLink.exe      # Programa de extracción de datos
└── README.md              # Este archivo
```

## 🎯 Funcionalidades

### Sistema de Semáforos
- **Verde:** Carga normal (0-80%)
- **Amarillo:** Carga moderada (80-95%)
- **Rojo:** Carga crítica (95-100%)
- **Negro:** Sobrecarga (>100%)

### Procesamiento de Datos
- Extracción automática de datos con PI_BridgeLink.exe
- Filtrado de eventos de cargabilidad
- Cálculo automático de porcentajes de carga
- Optimización de rendimiento

### Sistema de Evaluaciones
- Marcado de eventos como "Cuenta" o "No cuenta"
- Justificaciones personalizadas
- Guardado automático de evaluaciones
- Historial de evaluaciones

### Exportación
- Exportación a Excel con formato profesional
- Exportación a CSV para análisis externos
- Reportes detallados por nodo
- Estadísticas de cargabilidad

## 🔧 Instalación

1. **Requisitos:**
   - XAMPP (Apache + PHP 7.4+)
   - Git

2. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Emerson3101/Cargabilidad.git
   ```

3. **Configurar en XAMPP:**
   - Copiar la carpeta `Cargabilidad` a `htdocs/`
   - Acceder via `http://localhost/Cargabilidad/`

4. **Configuración inicial:**
   - Verificar permisos de escritura en archivos JSON
   - Configurar PI_BridgeLink.exe si es necesario

## 📊 Uso del Sistema

### Acceso Principal
- URL: `http://localhost/Cargabilidad/`
- Interfaz principal con sistema de semáforos
- Tabla de eventos de cargabilidad

### Procesamiento de Datos
1. Seleccionar fechas de inicio y fin
2. Hacer clic en "Procesar Datos"
3. El sistema ejecutará PI_BridgeLink.exe automáticamente
4. Los resultados se mostrarán con semáforos

### Evaluación de Eventos
1. Marcar checkboxes "Cuenta" o "No cuenta"
2. Agregar justificaciones si es necesario
3. Guardar evaluaciones
4. Los cambios se reflejan en las métricas

### Exportación
1. Usar botones "Exportar a Excel" o "Exportar a CSV"
2. Los archivos se descargan automáticamente
3. Incluyen todas las métricas y evaluaciones

## 🔐 Autenticación

- **Contraseña por defecto:** `123`
- **Acceso:** Botón "Admin" en la barra de navegación
- **Funciones:** Administración de tags y configuración

## 📈 Métricas del Sistema

- **Total de Eventos:** Número total de eventos de cargabilidad
- **Eventos Válidos:** Eventos marcados como "Cuenta"
- **Eventos Justificados:** Eventos marcados como "No cuenta"
- **Porcentaje de Carga:** Promedio de carga por nodo

## 🎨 Características de Diseño

- **Sistema de Semáforos:** Visualización intuitiva del estado de carga
- **Tarjetas Responsive:** Diseño moderno con tarjetas
- **Optimizado:** Carga rápida con grandes volúmenes de datos
- **Profesional:** Diseño corporativo con logo CFE

## 🔄 Actualizaciones

### Versión 2025
- Sistema de semáforos visual
- Interfaz de tarjetas modernizada
- Exportación mejorada
- Optimización de rendimiento
- Sistema de paginación

## 👨‍💻 Autor

**Emerson Salvador Plancarte Cerecedo**
- Email: emersonsalvador07@hotmail.com
- GitHub: [Emerson3101](https://github.com/Emerson3101)

## 📄 Licencia

Este proyecto es propiedad de la Comisión Federal de Electricidad (CFE) y está destinado para uso interno de la ZOTGM.

---

**Zona de Operación de Transmisión Guerrero Morelos (ZOTGM)**
*Sistema de Monitoreo de Cargabilidad* 
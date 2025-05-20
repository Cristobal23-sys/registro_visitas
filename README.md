# Sistema de Registro de Visitas Empresariales

Este proyecto es una aplicación web diseñada para gestionar el registro de visitas en empresas, facilitando el control de acceso, seguimiento de visitantes y notificación automática al personal interno. El sistema proporciona una solución integral para la gestión de visitas desde su llegada hasta su salida.

---

## Índice
- [Descripción del Proyecto](#descripción-del-proyecto)
- [Funcionalidades](#funcionalidades)
- [Arquitectura del Sistema](#arquitectura-del-sistema)
- [Archivos Principales](#archivos-principales)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Estado del Proyecto](#estado-del-proyecto)
- [Cómo Ejecutar el Proyecto](#cómo-ejecutar-el-proyecto)

---

## Descripción del Proyecto

La aplicación permite registrar y gestionar visitas a la empresa, manteniendo un registro detallado de cada visitante, incluyendo información personal, detalles del vehículo, persona a la que visita y motivo de la visita. El sistema automatiza el proceso de notificación al personal interno y genera reportes para análisis y control administrativo. El objetivo principal es mejorar la seguridad, facilitar el control de acceso y optimizar la gestión de visitantes en las instalaciones.

---

## Funcionalidades

### Para Recepción/Seguridad:
- **Registro de visitas:** Ingresar datos del visitante (nombre, RUT), empresa a la que pertenece, patente del vehículo, persona a quien visita y motivo de la visita.
- **Control de entrada/salida:** Registro automático de hora de entrada y botón para marcar hora de salida.
- **Notificaciones automáticas:** Envío de correo electrónico al personal interno cuando recibe una visita.
- **Consulta de visitas activas:** Visualizar visitantes actualmente en las instalaciones.

### Para Administradores:
- **Gestión de personal interno:** Crear, editar, habilitar o deshabilitar registros del personal que puede recibir visitas.
- **Generación de reportes:** Exportar a Excel listados de visitas filtrados por rango de fechas.
- **Historial completo:** Acceso al registro histórico de todas las visitas realizadas.

---

## Arquitectura del Sistema

La aplicación está desarrollada utilizando una estructura moderna y escalable:

1. **Modelo-Vista-Controlador (MVC):** Para separar la lógica del negocio, la presentación y el control.
2. **Base de Datos Relacional:** Almacena información sobre visitantes, personal interno y registros de visitas.
3. **Sistema de Notificaciones:** Integración con servicios SMTP para envío automático de correos.
4. **Exportación de Datos:** Generación de reportes en formato Excel para análisis posteriores.

---

## Archivos Principales

### `index.php`
Página principal del sistema en donde se encontrara de manera resumida todos los accesos al sistema ademas de encontrar un conteo de los visitantes actuales y las ultimas visitas activas.

### `registro.php`
Formulario para ingresar nuevos visitantes con todos los campos requeridos: nombre, RUT, empresa, patente, persona a visitar y motivo. Registra automáticamente la hora de entrada y envía la notificación por correo.

### `listado.php`
Muestra las visitas actualmente en curso, permitiendo marcar la hora de salida cuando el visitante abandona las instalaciones.

### `empleados.php`
Panel para gestionar al personal interno que puede recibir visitas. Permite crear nuevos registros, editar existentes, y habilitar/deshabilitar según sea necesario.

### `functions.php`
Generación de reportes personalizables con filtros por rango de fechas. Permite exportación a formato Excel.

### `config.php`
Configuración general del sistema, incluyendo ajustes de correo electrónico, personalización de plantillas de notificaciones y etc.

---

## Tecnologías Utilizadas

- **Lenguaje Backend:** PHP
- **Base de Datos:** MySQL
- **Frontend:** HTML5, JavaScript
- **Frameworks:** Bootstrap para diseño responsivo
- **Bibliotecas Adicionales:** 
  - PHPMailer para envío de correos
  - PhpSpreadsheet para generación de reportes Excel

---

## Estado del Proyecto

🚧 Proyecto en desarrollo 🚧

Actualmente el sistema cuenta con todas las funcionalidades básicas implementadas y operativas. Se están realizando pruebas para optimizar el rendimiento y mejorar la experiencia del usuario. Próximamente se implementarán características adicionales como:

- Registro de visitas mediante código QR
- Aplicación móvil para personal interno
- Integración con sistema de control de acceso físico

---

## Cómo Ejecutar el Proyecto

1. **Requisitos previos:**
   - Servidor web con PHP 7.4 o superior
   - MySQL 5.7 o superior
   - Extensiones PHP: mysqli, mbstring, gd, xml

2. **Instalación:**
   - Clonar el repositorio en el directorio del servidor web
   - Importar el archivo `database.sql` para crear la estructura de la base de datos
   - Configurar los parámetros de conexión en `config.php`
   - Configurar los datos SMTP para el envío de correos


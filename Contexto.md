Requerimientos funcionales (Capital Humano)
1. Autenticación (Login)
Inicio de sesión.
Registrar todos los intentos de login.
Registrar:
IP
Fecha
Intento realizado
Registrar anomalías de acceso.
Máximo 3 intentos antes de bloquear el acceso.
2. Administración de usuarios

CRUD de usuarios administrativos.

Debe permitir:

Crear usuarios
Consultar usuarios
Modificar usuarios
No eliminar usuarios físicamente
Desactivar usuarios mediante un campo:
Activo = 1
Inactivo = 0
3. Administración de roles

Sistema de permisos.

Debe permitir:

Crear roles
Asignar varios roles a un usuario
Definir alcance por módulos
Usuarios con acceso total
Usuarios con acceso parcial
4. Gestión de colaboradores

Debe almacenar como mínimo:

Información personal
Primer nombre
Segundo nombre
Primer apellido
Segundo apellido
Sexo
Identificación
Fecha de nacimiento
Fotografía del colaborador
Información de contacto
Dirección
Correo personal
Teléfono
Celular
Información laboral
Sueldo
Departamento
Fecha de contratación
Empleado activo (1/0)
Tipo de empleado
Permanente
Eventual
Interino
Ocupación
Programador
Electricista
etc.
5. Historial de cargos

El sistema debe manejar movimientos laborales.

Debe permitir:

Ascensos
Promociones
Cambios de sueldo
Cambios de funciones
Historial de cargos

Regla importante:

Un colaborador puede tener muchos cargos históricos, pero solamente uno puede estar activo.

6. Estado del colaborador

Debe manejar estados como:

Vacaciones
Licencia
Incapacidad
7. Historial académico

Debe permitir subir un archivo PDF con el historial académico del colaborador.

8. Reportes

Reporte con datos del colaborador incluyendo salario.

9. Búsquedas

Los reportes deben permitir búsqueda por:

Sexo
Edad
Nombre
Apellido
10. Exportación

Exportar informes en formato Excel.

11. Paginación

Todos los reportes deben incluir paginación.

12. Estadísticas

Debe generar estadísticas como:

Colaboradores por sexo
Colaboradores por edad
Colaboradores dentro de rangos de edad
Ejemplo:
20-25
25-30
30-35
13. Interfaz

El sistema debe incluir:

CSS
Menús horizontales
Opción HOME para regresar al menú principal desde cualquier módulo.
14. Consulta por dirección

Debe permitir visualizar colaboradores según su dirección.

15. Vacaciones

Debe existir un módulo que:

Calcule automáticamente las vacaciones.

Consideraciones:

1 día por cada 11 días trabajados.
O un mes por cada 11 meses trabajados.

Además:

Registrar solicitudes de vacaciones.
Registrar fechas de salida.
Simular colaboradores con más de 11 meses laborando.
16. API REST

Debe existir una API REST para entregar únicamente a la Contraloría General:

Cantidad de colaboradores por sexo.
Requerimientos técnicos obligatorios
La conexión a la base de datos debe realizarse mediante una clase.
Implementar control de errores.
Implementar una clase para sanitizar y validar datos.
Utilizar interfaces para el control de errores.
Aplicar:
OWASP
DRY
SOLID


Requisitos generales del proyecto
Arquitectura
MVC obligatorio.
Clases separadas por módulo.
Modelos.
Vistas.
Controladores.
UML

La documentación debe incluir:

Actores
Casos de uso
Descripción de casos de uso
Diagrama de clases
Relaciones entre clases
Diagrama de estados
Diagrama de secuencia
DER
Diccionario de datos
Base de datos

Debe incluir:

Script SQL
Backup
Datos semilla
Publicado en GitHub
Video

Debe existir un video donde se muestre:

Funcionamiento del sistema
Validaciones
Seguridad
Explicación del proyecto
README requerido

Debe contener como mínimo:

Información general
Nombre del proyecto
Integrantes
Roles
Versión
Video
Instalación
PHP requerido
MySQL/MariaDB
Servidor (XAMPP o Laragon)
Cómo clonar
Cómo configurar la BD
Backup
Credenciales

Tabla con usuarios de prueba.

Ejemplo:

Administrador

Operador

Manual de usuario

Con capturas de pantalla y explicación del flujo.

Requisitos no funcionales
RNF-01 Seguridad (OWASP)

Debe implementar:

Tokens CSRF
Consultas preparadas (PDO)
Protección contra SQL Injection
RNF-02 DRY

No duplicar código.

Centralizar:

Conexión BD
Validaciones
Respuestas JSON
RNF-03 SOLID
Responsabilidad única
Baja dependencia
Alta cohesión
Capas separadas
RNF-04 Login seguro
Bloqueo después de 3 intentos.
Registro de todos los intentos.
Bitácora de auditoría.
RNF-05 Contraseñas

Validar:

Mínimo 8 caracteres.
Máximo 12 caracteres.

Las validaciones deben hacerse en el backend.

RNF-06 Integridad mediante firmas digitales

Los datos críticos deben protegerse mediante una firma criptográfica.

En este proyecto, el ejemplo indicado es:

Salarios

Firmar información como:

ID del empleado
Cédula
Salario
Fecha

Al consultar el registro:

recalcular la firma;
si no coincide, detectar la alteración y alertar sobre una violación de integridad.
Resumen

En total, el proyecto contempla aproximadamente:

16 módulos o funcionalidades principales.
6 requisitos no funcionales obligatorios.
Arquitectura MVC.
Documentación UML completa.
README técnico.
Video demostrativo.
API REST.
Exportación a Excel.
Control de roles y permisos.
Auditoría y seguridad (OWASP, DRY, SOLID, CSRF, firmas digitales y bitácora de accesos).


Prompt:



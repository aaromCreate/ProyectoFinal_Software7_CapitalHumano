# Manual de usuario — Sistema de Capital Humano

## 1. Introducción

El Sistema de Capital Humano permite gestionar colaboradores, perfiles laborales, usuarios, roles, vacaciones y reportes, cumpliendo con requisitos de seguridad y auditoría.

## 2. Requisitos previos

- PHP 8.1 o superior.
- MySQL/MariaDB.
- Servidor web (Apache/Nginx).
- Composer.

## 3. Instalación

1. Clonar o copiar el proyecto al directorio del servidor web.
2. Ejecutar `composer install`.
3. Copiar `.env.example` a `.env` y configure la base de datos.
4. Importar `database/capital_humano.sql`.
5. Generar llaves OpenSSL en `config/keys/` si no existen.
6. Abrir la URL pública.

## 4. Inicio de sesión

**Credenciales de prueba:**

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| admin | Admin123! | Administrador |

<img alt="Inicio de sesión" src="https://github.com/user-attachments/assets/18db1d98-58db-4d8e-aa20-f1cd4730355f" width="100%" />

## 5. Menú principal

El menú horizontal muestra las opciones disponibles según los permisos del rol. Desde cualquier pantalla se puede presionar **HOME** para regresar al inicio.

<img alt="Menú principal" src="https://github.com/user-attachments/assets/a8956b45-acd9-44cf-b951-1fee9ec5b5da" width="100%" />

## 6. Gestión de colaboradores

### 6.1 Registrar colaborador

1. Ir a **Nuevo colaborador**.
2. Completar datos personales, contacto y perfil laboral.
3. Subir fotografía (opcional).
4. Guardar.

<img alt="Registrar colaborador" src="https://github.com/user-attachments/assets/03ddca56-6f20-4ce7-8521-c7392eab1331" width="100%" />

### 6.2 Ver detalle

En el detalle se muestra la fotografía, datos personales, historial académico y el historial laboral con el indicador de integridad OpenSSL.

<img alt="Ver detalle parte 1" src="https://github.com/user-attachments/assets/fcfe004c-23a3-43e8-ba0d-bc70605118a0" width="100%" />
<img alt="Ver detalle parte 2" src="https://github.com/user-attachments/assets/4a5a5d13-2781-4302-9c20-22c17a021d1a" width="100%" />

### 6.3 Editar

Al editar se cargan los datos actuales, incluyendo el perfil laboral activo.

<img alt="Editar colaborador" src="https://github.com/user-attachments/assets/32a9894a-a781-4676-932d-f69a613ec7fe" width="100%" />

### 6.4 Promociones y bajas

- **Promoción:** crea un nuevo cargo y desactiva el anterior.
  <img alt="Promoción de colaborador" src="https://github.com/user-attachments/assets/4f66501b-a880-47e8-90cf-c73a64543be6" width="100%" />

- **Baja:** registra fecha fin, motivo y desactiva al colaborador.
  <img alt="Baja de colaborador" src="https://github.com/user-attachments/assets/17c7c9e6-46ab-431e-919f-c846473fd5a3" width="100%" />

## 7. Reportes

1. Ir a **Reporte**.
2. Usar los filtros de texto, sexo y rango de edad.
3. Presionar **Buscar**.
4. Usar los números de página para navegar.
5. Presionar **Exportar Excel** para descargar el informe.

<img alt="Sección de reportes" src="https://github.com/user-attachments/assets/ba9802a1-d1e3-4cd1-98cd-22666ae819d5" width="100%" />

## 8. Vacaciones

1. Ir a **Vacaciones**.
2. Crear nueva solicitud seleccionando colaborador y fechas.
3. El sistema calcula automáticamente los días generados.
4. Un usuario autorizado puede aprobar o rechazar.

<img alt="Solicitud de vacaciones" src="https://github.com/user-attachments/assets/ec1ae123-4d16-46dd-b008-66a33e4425b9" width="100%" />
<img alt="Aprobación de vacaciones" src="https://github.com/user-attachments/assets/c745671d-a604-4bbd-a620-a99c3d6200a6" width="100%" />

## 9. Usuarios y roles

Solo el administrador puede crear usuarios, asignarles múltiples roles y definir permisos por módulo.

<img alt="Gestión de usuarios" src="https://github.com/user-attachments/assets/d904316f-3655-4b9a-8dba-46ca4231677c" width="100%" />
<img alt="Asignación de roles" src="https://github.com/user-attachments/assets/0a9d1060-c817-47ab-b359-6922d4263303" width="100%" />

## 10. Bitácora

Registra cada intento de acceso, indicando éxito, fracaso o anomalía después de 3 intentos fallidos.

<img alt="Bitácora de auditoría" src="https://github.com/user-attachments/assets/997c3f8e-3f61-46fe-ac31-19747a2eef44" width="100%" />

## 11. Consideraciones de seguridad

- Los formularios incluyen token CSRF.
- Las contraseñas se almacenan con `password_hash`.
- Las consultas usan PDO preparado.
- Los datos sensibles de perfiles laborales se firman con OpenSSL.

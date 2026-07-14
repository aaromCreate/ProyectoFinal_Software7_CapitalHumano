# Diccionario de datos

## colaboradores

| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador único del colaborador. |
| identidad | VARCHAR(50) | Documento de identidad. |
| primer_nombre | VARCHAR(100) | Primer nombre. |
| segundo_nombre | VARCHAR(100) | Segundo nombre (opcional). |
| primer_apellido | VARCHAR(100) | Primer apellido. |
| segundo_apellido | VARCHAR(100) | Segundo apellido (opcional). |
| fecha_nacimiento | DATE | Fecha de nacimiento. |
| sexo | ENUM | Masculino, Femenino u Otro. |
| direccion | TEXT | Dirección física. |
| correo | VARCHAR(150) | Correo electrónico. |
| telefono | VARCHAR(20) | Teléfono fijo (opcional). |
| celular | VARCHAR(20) | Teléfono celular. |
| estado_colaborador_id | INT FK | Activo, Vacaciones, Licencia, Incapacidad, Baja. |
| empleado_activo | TINYINT | 1 si tiene un cargo activo, 0 si no. |
| motivo_baja_id | INT FK | Motivo de terminación (opcional). |
| fotografia | VARCHAR(255) | Ruta de la fotografía (opcional). |
| fecha_registro | TIMESTAMP | Fecha de creación del registro. |

## perfiles_laborales

| Columna | Tipo | Descripción |
|---------|------|-------------|
| id | INT PK | Identificador del perfil. |
| colaborador_id | INT FK | Colaborador dueño del perfil. |
| ocupacion_id | INT FK | Ocupación desempeñada. |
| tipo_empleado_id | INT FK | Tipo de empleado. |
| planilla_id | INT FK | Tipo de planilla. |
| departamento_id | INT FK | Departamento (opcional). |
| salario | DECIMAL(10,2) | Salario del cargo. |
| fecha_inicio | DATE | Inicio del cargo. |
| fecha_fin | DATE | Fin del cargo (opcional). |
| cargo_activo | TINYINT | 1 si es el cargo actual. |
| es_activo | TINYINT | Bandera genérica de activo. |
| empleado_activo | TINYINT | 1 si el empleado está activo en este cargo. |
| motivo_terminacion_id | INT FK | Motivo de terminación (opcional). |
| firma_integridad | TEXT | Firma OpenSSL del perfil. |
| fecha_registro | TIMESTAMP | Fecha de creación. |

## usuarios, roles, permisos

| Tabla | Descripción |
|-------|-------------|
| usuarios | Usuarios administrativos del sistema. |
| roles | Perfiles de acceso. |
| permisos | Permisos por módulo y acción. |
| usuario_rol | Relación muchos a muchos entre usuarios y roles. |
| rol_permiso | Relación muchos a muchos entre roles y permisos. |

## Otras tablas

| Tabla | Descripción |
|-------|-------------|
| historial_academico | Títulos e instituciones con PDF adjunto. |
| solicitudes_vacaciones | Solicitudes de vacaciones de los colaboradores. |
| bitacora_accesos | Registro de intentos de login. |
| cat_estados_colaborador | Catálogo de estados. |
| cat_ocupaciones | Catálogo de ocupaciones. |
| cat_tipoempleado | Catálogo de tipos de empleado. |
| cat_tipos_planilla | Catálogo de tipos de planilla. |
| cat_motivos_terminacion | Catálogo de motivos de baja. |
| departamentos | Departamentos de la organización. |

# Documentación UML — Sistema de Capital Humano

> Los diagramas están escritos en sintaxis Mermaid. Se pueden visualizar en GitHub, GitLab o con la extensión Mermaid de VS Code.

## 1. Actores

| Actor | Descripción |
|-------|-------------|
| Administrador | Acceso total: usuarios, roles, colaboradores, reportes, vacaciones. |
| Operador | Gestión diaria de colaboradores, vacaciones y reportes. |
| Consultor | Solo lectura de reportes. |
| Contraloría | Consume la API REST de colaboradores por sexo. |

## 2. Casos de uso

```mermaid
graph LR
    A[Administrador]
    O[Operador]
    C[Consultor]
    S[Sistema]

    subgraph Módulos
        UC1[Iniciar sesión]
        UC2[Gestionar usuarios]
        UC3[Gestionar roles y permisos]
        UC4[Gestionar colaboradores]
        UC5[Registrar promociones]
        UC6[Registrar bajas]
        UC7[Ver reportes]
        UC8[Exportar Excel]
        UC9[Solicitar vacaciones]
        UC10[Consultar API]
    end

    A --> UC1 & UC2 & UC3 & UC4 & UC5 & UC6 & UC7 & UC8 & UC9
    O --> UC1 & UC4 & UC5 & UC6 & UC7 & UC8 & UC9
    C --> UC1 & UC7
    S --> UC10
```

## 3. Diagrama de clases

```mermaid
classDiagram
    class Database {
        +getConnection() PDO$
    }
    class Colaborador {
        +all(search)
        +paginated(search, page, perPage, sexo, edad)
        +report(search, sexo, edad)
        +find(id)
        +create(data)
        +update(id, data)
        +promote(id, data)
        +baja(id, data)
        +delete(id)
    }
    class Usuario {
        +findByUsername(u)
        +create(data)
        +update(id, data)
        +toggle(id)
        +logAccess(...)
    }
    class Rol {
        +all()
        +create(data)
        +update(id, data)
        +toggle(id)
    }
    class Vacacion {
        +all()
        +create(data)
        +aprobar(id)
        +rechazar(id)
        +diasGenerados(id)
    }
    class AuthService {
        +login(u, p)
        +logout()
        +can(modulo, accion)
        +ensure(modulo, accion)
    }
    class IntegrityService {
        +signPerfilLaboral(data)
        +verifyPerfilLaboral(data, signature)
    }
    class FileUploadService {
        +uploadImage(file, folder)
        +uploadPdf(file)
    }
    class ExcelExportService {
        +download(rows)
    }
    class Validator {
        +colaborador(data, catalogos)
        +usuario(data, requirePassword)
        +rol(data)
        +vacacion(data)
    }
    class Sanitizer {
        +text(v)
        +int(v)
        +money(v)
        +colaborador(data)
    }

    Database <-- Colaborador
    Database <-- Usuario
    Database <-- Rol
    Database <-- Vacacion
    AuthService ..> Usuario
    Colaborador ..> IntegrityService
    Colaborador ..> FileUploadService
    ReporteController ..> ExcelExportService
    ColaboradorController ..> Validator
    ColaboradorController ..> Sanitizer
```

## 4. Diagrama de estados — Colaborador

```mermaid
stateDiagram-v2
    [*] --> Activo: Contratación
    Activo --> Vacaciones: Solicitud aprobada
    Vacaciones --> Activo: Regreso
    Activo --> Licencia
    Activo --> Incapacidad
    Activo --> Baja: Terminación
    Vacaciones --> Baja
    Licencia --> Baja
    Incapacidad --> Baja
    Baja --> [*]
```

## 5. Diagrama de secuencia — Login

```mermaid
sequenceDiagram
    actor U as Usuario
    participant F as Navegador
    participant C as AuthController
    participant S as AuthService
    participant M as Usuario

    U->>F: Envia usuario y contraseña
    F->>C: POST login.process
    C->>S: login(usuario, password)
    S->>M: findByUsername(usuario)
    M-->>S: datos del usuario
    alt credenciales válidas
        S->>M: resetAttempts, logAccess
        S-->>C: OK
        C-->>F: redirect home
    else credenciales inválidas
        S->>M: incrementFailedAttempts
        S->>M: logAccess(anomalia)
        S-->>C: RuntimeException
        C-->>F: mensaje de error
    end
```

## 6. Diagrama entidad-relación (DER)

```mermaid
erDiagram
    COLABORADORES ||--o{ PERFILES_LABORALES : tiene
    COLABORADORES ||--o{ HISTORIAL_ACADEMICO : posee
    COLABORADORES ||--o{ SOLICITUDES_VACACIONES : solicita
    COLABORADORES }o--|| CAT_ESTADOS_COLABORADOR : "está en"
    PERFILES_LABORALES }o--|| CAT_OCUPACIONES : "ocupa"
    PERFILES_LABORALES }o--|| CAT_TIPOEMPLEADO : "es"
    PERFILES_LABORALES }o--|| CAT_TIPOS_PLANILLA : "pertenece"
    PERFILES_LABORALES }o--o| DEPARTAMENTOS : "pertenece"
    USUARIOS }o--o{ ROLES : "tiene"
    ROLES }o--o{ PERMISOS : "posee"
```

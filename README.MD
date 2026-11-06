# Proyecto: Turnos y Reservas

## ADRs

ADR significa Architecture Decision Record, o en español, “registro de decisión de arquitectura”. Es un documento corto, de una página o menos, donde anotás una decisión técnica importante y el razonamiento detrás de ella, para que dentro de seis meses (o cuando alguien lo lea en una entrevista) se entienda por qué el proyecto es como es.

### La estructura típica tiene pocas secciones

**Título y estado:** por ejemplo, “ADR 001: usar autenticación integrada de Laravel”, con estado propuesto, aceptado o reemplazado

**Contexto:** qué problema o necesidad había

**Decisión:** qué elegiste

**Alternativas descartadas:** qué otras opciones evaluaste y por qué no

**Consecuencias:** qué ganás y qué costo o limitación aceptás

## ADRs del proyecto

### ADR 001: decisiones de entorno

**Estado:** Aceptado

**Contexto:** Inicio del proyecto de turnos y reservas para el portafolios

**Decisión:**

- Backend: Laravel 13.38 con Sail. Los servicios extra (base de datos, Redis, Mailpit) se activan desde Sail
- Frontend: Starter kit de Laravel con Vue 3 (Inertia + TypeScript + Tailwind)
- Calidad: Pest, PHPStan, Pint y GitHub Actions
- IA:
    - `laravel/ai` (AI SDK): para construir funciones de IA dentro de la app (agentes, herramientas, salida estructurada)
    - `laravel/mcp`: para exponer la app a clientes de IA externos como servidor MCP
    - `laravel/boost` (solo desarrollo): da contexto de Laravel al agente que escribe código (esquema de BD, rutas, logs, documentación versionada, guidelines propias en `.ai/guidelines`)

**Alternativas descartadas:**

- Docker y docker compose vanilla No se arma Docker desde cero (extenso y propenso a problemas de permisos en Linux)
- Frontend con laravel livewire, react, svelte o laravel blade vanilla, se usa Vue por su curva de aprendizaje suave, su demanda en el mercado y su buen ajuste con interfaces interactivas (kanban, dashboards), manteniendo la lógica en el backend gracias a Inertia. Livewire se descarta porque limita la transferencia de habilidades fuera del ecosistema Laravel y resulta menos natural para el drag and drop

**Consecuencias:**

- Dos ecosistemas que mantener: PHP y Node conviven, con dos gestores de paquetes y un paso de build
- Inertia acopla el frontend a Laravel: no hay una API pública para otros clientes
- MySQL no tiene restricciones de exclusión por rango: otras bases, como PostgreSQL, pueden impedir a nivel de base que dos reservas se superpongan. En MySQL esa garantía se tiene que construir con transacciones y bloqueo de filas, y probarla con tests de concurrencia
- Despliegue más exigente: la demo online necesita PHP, MySQL, un proceso de cola y, si se deja activado el renderizado del lado del servidor de Inertia, también un proceso de Node. Eso descarta un hosting compartido por un VPS o una nube

### ADR 002: autenticación integrada de Laravel en lugar de WorkOS

**Estado:** Aceptado

**Contexto:** El sistema necesita roles cliente/admin con Policies sobre la tabla users, y el servidor MCP se autentica con Sanctum

**Decisión:** Usar la autenticación integrada del starter kit, con email verification, registration y password confirmation

**Alternativas descartadas:**

- WorkOS: agrega una cuenta y claves externas, y la demo dependería de un tercero
- Two-factor y passkeys: agregan tablas y pantallas fuera del alcance del proyecto

**Consecuencias:** Más control sobre usuarios y roles, y menos dependencias para quien clone el repo. A cambio, si hiciera falta SSO más adelante, habría que implementarlo a mano

### ADR 003: Railway como hosting del proyecto, en formato monolito

**Estado:** Aceptado

**Contexto:** El proyecto necesita app web, worker de colas y MySQL, con un
presupuesto mínimo. La interfaz se sirve con Inertia desde el propio Laravel.

**Decisión:** Desplegar el monolito completo en Railway.

**Alternativas descartadas:**

- VPS propio: costo mensual fijo y mantenimiento del servidor.
- Backend en Railway y frontend en Netlify: no reduce el costo (el consumo
  está en el backend) y obliga a reescribir la autenticación como API + SPA.
- Vercel o Netlify para todo: no encajan con PHP, colas ni MySQL.

**Consecuencias:** Un único despliegue y un único pipeline. Hay que preparar
una build de producción, logs a stderr, servicios separados (web, worker,
MySQL) y SSR de Inertia desactivado. Railway es adecuado para demos, no para
producción de largo plazo.

### ADR-004: versión de PHP alineada entre Sail y CI

- **Estado:** aceptado
- **Fecha:** 04/11/2026

**Contexto:**

El 20 de octubre de 2026 se filtraron dos vulnerabilidades críticas en el
núcleo de PHP (CVE-2026-109618 y CVE-2026-109619, conocidas como php2shell)
que permiten ejecución remota de código sin autenticación. El fallo está en
el motor, por lo que el código de la aplicación no alcanza a mitigarlo. Los
parches salieron el 21 de octubre en 8.5.12, 8.4.27, 8.3.36 y 8.2.35.
La imagen de Sail del proyecto se construye con PHP 8.5, mientras el CI
estaba planteado en 8.3, así que se probaba contra una versión distinta de
la de desarrollo.

**Decisión:**

Usar PHP 8.5 en Sail y en el CI, con 8.5.12 como versión mínima. El pipeline
incluye un paso que falla si la versión de PHP es menor. La misma versión se
fija en el despliegue.

**Alternativas descartadas:**

- Fijar Sail en PHP 8.3 (8.3.36): exige tocar el runtime de Sail y
  alejarse de su configuración por defecto, sin una ventaja concreta para el
  proyecto.
- Dejar el CI en 8.3: los tests correrían en una versión que no es la de
  desarrollo ni la de producción.
- No verificar la versión en el CI: un entorno desactualizado pasaría
  desapercibido y el pipeline daría una falsa sensación de seguridad.

**Consecuencias:**

- Desarrollo, CI y producción comparten versión de PHP.
- Una versión vulnerable rompe el pipeline en lugar de llegar a producción.
- Al salir un parche nuevo hay que subir el mínimo en el CI y reconstruir la
  imagen de Sail con `sail build --no-cache`.

### ADR-005: MySQL real en el CI en lugar de SQLite

- **Estado:** aceptado
- **Fecha:** 04/11/2026

**Contexto:**

El núcleo del proyecto es evitar reservas superpuestas bajo concurrencia, lo
que requiere transacciones y bloqueo de filas (`lockForUpdate`). SQLite no
implementa ese bloqueo de la misma forma, por lo que los tests de
concurrencia podrían pasar en CI y fallar en producción.

**Decisión:**

El CI levanta un servicio MySQL 8.0 y ejecuta los tests contra él, igual que
Sail en desarrollo y Railway en producción.

**Alternativas descartadas:**

- SQLite en memoria: más rápido y simple, pero invalida los tests de
  concurrencia, que son lo más valioso del proyecto.
- Mockear el bloqueo de filas: prueba el mock y no el comportamiento real
  de la base de datos.

**Consecuencias:**

- Los tests de concurrencia reflejan el motor real.
- El pipeline tarda unos segundos más por el arranque del servicio MySQL.
- Las credenciales del servicio son solo del entorno efímero del CI, no
  secretos reales.

### ADR 006: Adopción del Modelo de Negocio Unificado (Enfoque A) para el Sistema de Reservas

- **Estado:** Aceptado
- **Fecha:** 04/11/2026

**Contexto:**
Estamos desarrollando una demo en Laravel 13 orientada a un sistema de turnos y reservas integrado con un agente de IA (AI SDK) y un servidor MCP. Necesitamos definir el alcance de los roles de usuario (`admin` y `client`) y la arquitectura de datos subyacente para las consultas de disponibilidad y prevención de superposiciones, manteniendo una complejidad baja y un alto rendimiento operativo.

**Decisión:**
Adoptar el **Enfoque A (Modelo Unificado)**:

- El rol **`admin`** gestiona de manera centralizada la creación y configuración de los servicios (ej. duración, precios).
- El rol **`client`** se limita exclusivamente a consultar disponibilidad y reservar dichos servicios.
- Se descarta la lógica de múltiples proveedores o perfiles cruzados.

**Alternativas descartadas:**

- **Modelo Marketplace / Proveedores múltiples (Enfoque B):** Permitir que cualquier usuario preste servicios y a la vez reserve. Se descartó porque añade una capa innecesaria de complejidad (roles/permisos avanzados, relaciones polimórficas y múltiples agendas cruzadas) que dispersa el foco principal de la demo: el AI SDK, el servidor MCP y la lógica anti-superposición.

**Consecuencias:**

- **Positivas:**
- Lógica de validación de disponibilidad altamente directa y eficiente (consultas indexadas sobre la tabla `bookings`).
- Desarrollo más limpio y mantenible para las herramientas (tools) del agente de IA y el servidor MCP.
- Menor fricción en la gestión de permisos (`enum` simple en la tabla `users`).

- **Negativas:**
- Los clientes no pueden ofrecer servicios propios dentro de la plataforma, limitando el sistema a un esquema de negocio único (ej. prestador independiente con un panel de administración).

---

#### Esquema de Datos Asociado

```
+-------------------+       +-----------------------+       +-------------------+
|       users       |       |       bookings        |       |     services      |
+-------------------+       +-----------------------+       +-------------------+
| id (PK)           |1     *| id (PK)               |*     1| id (PK)           |
| name              |-------| user_id (FK)          |-------| name              |
| email             |       | service_id (FK)       |       | description       |
| password          |       | start_time            |       | duration_minutes  |
| role              |       | end_time              |       | price             |
+-------------------+       | status                |       | is_active         |
                            | notes                 |       +-------------------+
                            +-----------------------+

```

### ADR 00..: titulo

**Estado:**

**Contexto:**

**Decisión:**

**Alternativas descartadas:**

**Consecuencias:**

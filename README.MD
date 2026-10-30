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

### ADR 00..: decisiones de entorno

**Estado:**

**Contexto:**

**Decisión:**

**Alternativas descartadas:**

**Consecuencias:**

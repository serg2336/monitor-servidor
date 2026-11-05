# Monitor de Servidor + Logging de Seguridad

Trabajo práctico de Programación IV (UTN FRRe). Sistema desarrollado en Laravel 13 que monitorea los recursos del sistema operativo (Windows) y registra intentos de autenticación para detectar ataques de fuerza bruta.

## Sistema Operativo Usado
Windows 11 (con XAMPP, PHP 8.3, MySQL).

## Funcionalidades
1. **Monitor del Servidor:** Lee datos de hardware (CPU, memoria, disco, temperatura) usando PowerShell (WMI) y PHP. Los expone en una vista web con polling y en una API JSON.
2. **Logging y Detección:** Registra logins exitosos, fallidos y bloqueados. Aplica bloqueos automáticos de 10 minutos tras 5 fallos en 5 minutos.

---

## Respuestas - TP1 (Monitor de Servidor)

**1. ¿Por qué la temperatura suele aparecer como “No disponible” en Windows?**
Porque la clase WMI `MSAcpi_ThermalZoneTemperature` requiere privilegios de administrador para ser consultada, y además no todas las placas madres exponen los sensores de temperatura a través de esta interfaz.

**2. ¿Por qué el script de PowerShell nunca debe incluir datos que vengan del usuario?**
Porque el método `shell_exec()` ejecuta comandos directamente en el sistema operativo. Si se concatena la entrada del usuario en el script de PowerShell, un atacante podría inyectar comandos maliciosos (Command Injection) y tomar el control del servidor.

**3. ¿Qué riesgo tiene dejar /api/metrics sin autenticación en un servidor real?**
Expone información sensible de la infraestructura (nombres de equipos, versiones de SO, uso de recursos) a cualquier persona en internet, lo que facilita la recopilación de información para planificar un ataque. En producción debería protegerse con Sanctum o similar.

**4. Con polling cada 5 segundos, ¿cuántos pedidos por minuto recibe el servidor si hay 50 personas mirando?**
50 personas * (60 segundos / 5 segundos) = **600 pedidos por minuto**.

**5. Si una app solo necesita la temperatura, ¿qué datos recibe de más al llamar a /api/metrics?**
Recibe todo el objeto JSON (hostname, SO, PHP, disco, memoria, CPU, uptime, etc.). A esto se le llama *over-fetching*, y es el problema principal que resuelve GraphQL al permitir pedir solo campos específicos.

---

## Respuestas - TP2 (Logging y Seguridad)

**1. ¿Qué IP concentra los fallos?, ¿puede afirmarse que existe un ataque?**
En entorno local, `127.0.0.1`. No se puede afirmar que sea un ataque real solo por la cantidad de fallos, ya que podría ser un usuario que olvidó su contraseña. Se necesita más contexto (user-agents, horarios, intentos de cuentas distintas).

**2. ¿Qué efectos tendría bloquear una IP compartida?**
Podría dejar sin acceso a usuarios legítimos que comparten la misma IP (por ejemplo, en una oficina con NAT o una universidad). Por eso es mejor combinar límites por IP con límites por cuenta de usuario.

**3. ¿Qué evidencias adicionales se necesitan?**
Registros de user-agent, geolocalización de la IP, patrones de horarios, y saber si los intentos fueron dirigidos a múltiples cuentas (password spraying) o a una sola.

**4. ¿Qué respuestas conviene automatizar?**
Los bloqueos temporales de IP, las alertas en el log de seguridad y las notificaciones al administrador. La intervención humana debe quedar para investigar incidentes críticos o bloqueos permanentes.
# Monitor de Servidor + Logging de Seguridad

Trabajo práctico de Programación IV (UTN FRRe). Sistema desarrollado en Laravel que monitorea los recursos del sistema operativo (Windows) y registra intentos de autenticación para detectar ataques de fuerza bruta.

## 🖥️ Entorno de Desarrollo
- **Sistema Operativo:** Windows 11
- **Servidor Local:** XAMPP v3.3.0 (PHP 8.3.33)
- **Framework:** Laravel v13.40.0
- **Base de Datos:** MySQL (vía XAMPP)
- **Node.js / Vite:** Vite v8.3.5 (para compilar Tailwind CSS)

## 🛠️ Tecnologías y Paquetes Instalados
- **Laravel Breeze** (Autenticación con Blade y Tailwind CSS).
- **Tailwind CSS v4** + `@tailwindcss/vite` (Estilos visuales).
- **Laravel Sanctum** (Instalado con `install:api` para manejar el token de la API).
- **PowerShell + WMI** (Para leer los datos de hardware del sistema operativo).

## 🏗️ Algoritmo y Arquitectura de la Aplicación
El proyecto está dividido en dos grandes módulos:

### 1. Monitor del Servidor (Hardware)
- **`app/Services/ServerMetrics.php`**: Es el corazón del monitor. Utiliza `shell_exec()` para ejecutar un script de PowerShell (codificado en Base64 para evitar errores de comillas). Este script consulta WMI (`Win32_OperatingSystem` y `Win32_Processor`), devuelve los datos en JSON, y PHP los decodifica.
- **`ServerController.php`**: Devuelve la vista web (`/servidor`) y la respuesta JSON (`/api/metrics`).
- **Polling**: La vista `servidor.blade.php` usa un `setInterval` en JavaScript para consultar `/api/metrics` cada 5 segundos y actualizar las tarjetas.

### 2. Logging y Seguridad (TP2)
- **Canal de Logs**: Se configuró el canal `security` en `config/logging.php` que escribe en `storage/logs/security.log`.
- **Modelo `LoginAttempt`**: Guarda el hash del email, IP, estado (success/failed/blocked), user-agent y timestamp.
- **Listener `RecordSecurityAuthentication`**: Escucha los eventos `Illuminate\Auth\Events\Login` y `Failed` de Laravel para registrar los intentos.
- **Middleware `SecurityLoginGuard`**: Se ejecuta antes de procesar el login. Cuenta los fallos de la IP en los últimos 5 minutos:
  - 3 fallos: Dispara una alerta `ALERT` en el log.
  - 5 fallos: Bloquea la IP en caché por 10 minutos, lanza un log `CRITICAL` y devuelve un error HTTP 429.
- **Panel de Control (`/security-dashboard`)**: Muestra estadísticas de las últimas 24hs y una tabla con IPs sospechosas.

## 🚀 Comandos para Replicar el Proyecto desde Cero

Si necesitas clonar o reconstruir este proyecto, estos son los comandos que se ejecutaron en orden:

### 1. Crear el proyecto Laravel
```bash
composer create-project laravel/laravel monitor-servidor
cd monitor-servidor
2. Instalar API y Autenticación
bash
php artisan install:api
composer require laravel/breeze --dev
php artisan breeze:install blade
3. Configurar Frontend (Vite + Tailwind)
bash
npm install
npm install tailwindcss @tailwindcss/vite --save-dev
npm run dev # o npm run build para producción
4. Configurar Base de Datos
Crear la base de datos en MySQL y configurar el archivo .env con las credenciales.

bash
php artisan migrate
5. Crear los archivos del sistema
bash
php artisan make:controller ServerController
php artisan make:model LoginAttempt -m
php artisan make:listener RecordSecurityAuthentication
php artisan make:middleware SecurityLoginGuard
php artisan make:controller SecurityDashboardController

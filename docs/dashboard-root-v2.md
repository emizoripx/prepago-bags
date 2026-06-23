# Manual Técnico: Dashboard Root V2

## Introducción
Este documento detalla las implementaciones y modificaciones realizadas en la rama `DASHBOARD-ROOT-V2` para la evolución del Dashboard Root en la infraestructura de la versión 2 (basada en Tailwind y Livewire). El objetivo principal ha sido migrar y centralizar las vistas administrativas, ofrecer métricas de rendimiento y añadir capacidades de gestión avanzadas, como la personificación (impersonate) y la gestión de facturas.

## Novedades y Componentes Principales

### 1. Perfil Unificado de la Empresa (Company Profile)
Se ha creado una vista integral tipo "Perfil de Empresa" organizada en pestañas para facilitar la revisión de datos.
- **Ruta:** `dashboard/companies/{company_id}/profile`
- **Controlador:** `DashboardController@profile`
- **Componentes Livewire:**
  - `CompanyInformationTab`: Muestra la información general de la cuenta y detalles de facturación.
  - `CompanySettingsTab`: Muestra configuraciones específicas y preferencias de la empresa.
  - `CompanyUsersTab`: Listado y métricas relacionadas con los usuarios de la empresa.
- **Vistas Blade:**
  - `superadmin/company-profile.blade.php` (Vista principal)
  - `components/superadmin/company-information-tab.blade.php`
  - `components/superadmin/company-settings-tab.blade.php`
  - `components/superadmin/company-users-tab.blade.php`

### 2. Funcionalidad de Personificación (Impersonation)
Se ha introducido la capacidad para que los superadministradores inicien sesión temporalmente como el usuario propietario (owner) de una compañía específica, permitiendo auditoría y revisión de la cuenta desde la perspectiva del cliente.
- **Ruta:** `dashboard/companies/{company_id}/impersonate`
- **Controlador:** `DashboardController@impersonate`
- **Detalle de implementación:** Se busca en la tabla `company_user` al propietario (`is_owner = 1`), se recupera la cuenta de `User` y se usa `Auth::login($user)` para autorizar la sesión, redirigiendo posteriormente al dashboard de Invoice Ninja.

### 3. Gestión y Acciones sobre Facturas
Se han mejorado las acciones disponibles dentro de la tabla administrativa de facturas (`InvoicesAdminTable`). Ahora los administradores pueden realizar operaciones directamente desde la interfaz administrativa.
- **Visualización de PDF:** Enlace directo para visualizar la factura en formato PDF.
- **Edición:** Enlace para editar los detalles de la factura en la interfaz principal.
- **Regeneración de PDF:** Implementada a través del método `regeneratePdf` en Livewire, el cual despacha el trabajo estándar del sistema `\App\Jobs\Entity\CreateEntityPdf` asociado a la factura, permitiendo corregir PDFs corruptos o desactualizados.

### 4. Estadísticas de la Empresa
Para mejorar el control y proporcionar resúmenes eficientes, se ha implementado un nuevo modelo de métricas que permite almacenar y consultar estadísticas rápidas de las cuentas.
- **Modelo:** `src/Models/CompanyStatistic.php`
- **Migración:** `database/migrations/2026_06_22_210500_create_company_statistics_table.php`
- **Comando en Consola:** Se creó `UpdateCompanyStatisticsCommand` registrado en `PrepagoBagsServiceProvider.php` para la actualización automática o cronogramada de estas estadísticas de rendimiento y volumen de documentos.

## Actualización de Rutas
Se han agregado nuevas rutas bajo el archivo `src/routes/PrepagoBags.php` para soportar las nuevas funcionalidades:
```php
Route::get('dashboard/companies/{company_id}/profile', 'DashboardController@profile')->name('admin.companies.profile');
Route::get('dashboard/companies/{company_id}/impersonate', 'DashboardController@impersonate')->name('admin.companies.impersonate');
```

## Dependencias
- Requiere TailwindCSS para el maquetado de las nuevas vistas.
- Utiliza Laravel Livewire para la gestión reactiva de las pestañas en el perfil y las acciones de la tabla de facturas.
- Se integra con la arquitectura de Invoice Ninja para tareas de PDF (`CreateEntityPdf`).

## Consideraciones de Despliegue
1. Ejecutar las migraciones pendientes para la creación de la tabla de estadísticas: `php artisan migrate`
2. Si es necesario, registrar el comando de actualización de estadísticas en el Scheduler de Laravel (Kernel.php) para asegurar métricas actualizadas diariamente o de forma periódica.

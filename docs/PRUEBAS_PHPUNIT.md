# Pruebas automatizadas con PHPUnit

El proyecto utiliza PHPUnit 11 mediante el comando de Laravel `php artisan test`.

## Requisitos para ejecutar las pruebas localmente

1. Tener PHP y Composer instalados.
2. Tener iniciado MySQL/MariaDB.
3. Crear previamente una base de datos exclusiva para pruebas llamada `sabor_express_testing`.
4. Verificar que las credenciales de conexión de MySQL en el archivo `.env` sean correctas.

El archivo `phpunit.xml` fuerza el entorno `testing`, la conexión MySQL y el nombre de la base de datos `sabor_express_testing`. Las pruebas que utilizan `RefreshDatabase` trabajan sobre esa base de datos de pruebas, no sobre la base de datos de producción. No cambies el nombre de esa base por el de producción.

## Comandos disponibles

Se añadieron accesos directos en `composer.json`. Se ejecutan desde la raíz del proyecto:

| Comando | Qué ejecuta |
|---|---|
| `composer test` | Todas las pruebas automatizadas. |
| `composer test:unit` | Solo las pruebas unitarias de `tests/Unit`. |
| `composer test:feature` | Solo las pruebas funcionales de `tests/Feature`. |
| `composer test:admin` | Pruebas del módulo administrativo. |
| `composer test:cliente` | Pruebas del módulo cliente. |
| `composer test:delivery` | Pruebas del módulo Delivery. |
| `composer test:reportes` | Pruebas del módulo de reportes PDF. |
| `composer test:firebase` | Pruebas del servicio de eliminación de ubicaciones en Firebase (solicitudes HTTP simuladas). |
| `composer test:ocr` | Pruebas unitarias del análisis OCR. |

También se pueden utilizar directamente los comandos Artisan:

```powershell
php artisan test
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test --filter=ReportesTest
```

## Automatización en GitHub

El archivo `.github/workflows/phpunit.yml` ejecuta la suite completa automáticamente cuando se realiza un `push` a `main` o se abre/actualiza un pull request dirigido a `main`. El workflow prepara PHP, Node.js y un servicio MySQL temporal para las pruebas.

La ejecución en GitHub Actions usa una base de datos temporal de CI; no necesita ni accede a la base de datos del hosting.

## Archivos principales

- `phpunit.xml`: configuración de PHPUnit y conexión a la base de datos de pruebas.
- `tests/TestCase.php`: configuración base compartida por las pruebas Laravel.
- `tests/Feature/`: pruebas de los flujos y módulos de la aplicación.
- `tests/Unit/`: pruebas unitarias de servicios y lógica aislada.
- `.github/workflows/phpunit.yml`: ejecución automática de las pruebas en GitHub Actions.

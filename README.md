[README.md](https://github.com/user-attachments/files/35382230/README.md)
# Sistema de Gestión de Restaurante Online

Proyecto académico de la Tarea Práctica 6 — Unidad 3, Desarrollo de Sistemas en PHP.

## 1. Tecnologías utilizadas

- HTML5 para la estructura.
- Bootstrap 5.3 para el diseño adaptable.
- CSS3 para la identidad visual.
- JavaScript para confirmaciones, validaciones y cálculo de subtotal.
- PHP 8.1 o superior para el backend, con extensiones PDO y PDO MySQL habilitadas.
- MySQL/MariaDB y phpMyAdmin para la base de datos.
- PDO y consultas preparadas para el acceso a datos.

## 2. Funcionalidades por rol

| Rol | Permisos implementados |
|---|---|
| Administrador | Consultar el panel, crear/editar/retirar platos, crear/editar/desactivar usuarios, consultar todos los pedidos y cambiar sus estados. |
| Chef | Consultar todos los pedidos, pasar un pedido de Pendiente a En preparación y de En preparación a Listo para servir. |
| Camarero | Registrar pedidos en nombre de clientes, consultar sus pedidos asignados y marcarlos como Entregados cuando estén listos. |
| Cliente | Consultar el menú, registrar pedidos propios y revisar el estado e historial de sus pedidos. |

Los permisos se validan en el servidor con PHP; no dependen únicamente de ocultar botones en la interfaz.

## 3. Instalación en localhost con XAMPP

1. Instale e inicie Apache y MySQL desde XAMPP (o use Laragon).
2. Copie la carpeta `Tarea6_Restaurante` dentro de `C:\xampp\htdocs\`.
3. Abra `http://localhost/phpmyadmin`.
4. Importe el archivo `database.sql` en una base de datos nueva o vacía. El script crea la base `restaurante_online`, las tablas `roles`, `users`, `dishes` y `orders`, y platos de ejemplo. **Importarlo nuevamente elimina las tablas anteriores y sus datos**, por lo que no debe repetirse sobre una instalación con información que desee conservar.
5. Revise `config/database.php`. En XAMPP normalmente se utiliza:
   - Servidor: `localhost`
   - Base de datos: `restaurante_online`
   - Usuario: `root`
   - Contraseña: vacía (solo para instalación local típica).
6. Abra una terminal en la carpeta del proyecto y ejecute:

   ```bash
   php scripts/seed_demo.php
   ```

   Este comando crea las cuentas demostrativas usando `password_hash()` de PHP. El script está limitado a ejecución por terminal y no se puede ejecutar desde el navegador.
7. Abra `http://localhost/Tarea6_Restaurante/`.

## 4. Cuentas de demostración

| Perfil | Correo | Contraseña |
|---|---|---|
| Administrador | admin@restaurante.local | Admin123! |
| Chef | chef@restaurante.local | Chef123! |
| Camarero | camarero@restaurante.local | Camarero123! |
| Cliente | cliente@restaurante.local | Cliente123! |

**Seguridad:** son credenciales académicas de prueba. Cambie las contraseñas antes de publicar el sistema. No utilice estas cuentas en un entorno real.

## 5. Estructura del proyecto

```text
Tarea6_Restaurante/
├── assets/
│   ├── css/styles.css
│   └── js/app.js
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── bootstrap.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── scripts/
│   └── seed_demo.php
├── database.sql
├── dashboard.php
├── dish_delete.php
├── dish_save.php
├── index.php
├── login.php
├── logout.php
├── menu.php
├── order_create.php
├── order_save.php
├── order_status.php
├── orders.php
├── user_delete.php
├── user_save.php
└── users.php
```

## 6. Modelo de datos

El diseño conserva las entidades indicadas en la guía del profesor: `roles`, `users`, `dishes` y `orders`.

Se añadieron campos complementarios:
- `users.is_active` permite desactivar cuentas sin borrar el historial.
- `dishes.is_available` permite retirar platos del menú sin perder los pedidos históricos.
- `orders.waiter_id` identifica al camarero responsable del pedido.
- `created_at` y `updated_at` permiten conocer las fechas de registro y actualización.

La relación principal es:
- Un rol puede estar asignado a muchos usuarios.
- Un cliente puede registrar muchos pedidos.
- Un plato puede aparecer en muchos pedidos.
- Un camarero puede tener asignados varios pedidos.

## 7. Pruebas funcionales recomendadas

Realice estas pruebas en localhost con cada cuenta:

| N.º | Usuario de prueba | Acción | Resultado esperado |
|---|---|---|---|
| 1 | Administrador | Iniciar sesión | Accede al panel y al menú de administración. |
| 2 | Administrador | Crear, editar y retirar un plato | Los cambios se reflejan en el menú. |
| 3 | Administrador | Crear un usuario con rol Chef | El usuario queda registrado con ese rol. |
| 4 | Chef | Consultar pedidos | Visualiza los pedidos registrados. |
| 5 | Chef | Cambiar Pendiente a En preparación | El estado se actualiza. |
| 6 | Chef | Cambiar En preparación a Listo para servir | El estado se actualiza. |
| 7 | Camarero | Registrar un pedido para un cliente | El pedido queda asignado al camarero. |
| 8 | Camarero | Marcar como Entregado un pedido listo | El estado pasa a Entregado. |
| 9 | Cliente | Consultar el menú y realizar un pedido | El pedido queda registrado a su nombre. |
| 10 | Cliente | Consultar historial | Solo visualiza sus propios pedidos. |
| 11 | Cliente | Intentar abrir la administración | El servidor deniega el acceso. |

Registre capturas de pantalla de las pruebas realizadas para incluirlas como evidencia en la entrega. No marque una prueba como aprobada sin ejecutarla.

## 8. Publicación en un hosting

La guía permite utilizar un servicio gratuito como InfinityFree, FreeHosting u otro alojamiento compatible con PHP y MySQL.

Procedimiento general:

1. Cree una cuenta en el hosting y un sitio web.
2. Cree una base de datos MySQL desde el panel del proveedor.
3. Importe `database.sql` en la base de datos del hosting.
4. Ejecute `scripts/seed_demo.php` de forma local, si aún no lo hizo, y exporte desde phpMyAdmin la base de datos con los usuarios de prueba. Importe también esos datos al hosting; alternativamente, cree usuarios desde el panel de administración después de configurar una primera cuenta.
5. Edite `config/database.php` con el host, nombre de base, usuario y contraseña que entregue el proveedor.
6. Suba los archivos del proyecto al directorio público del hosting.
7. Abra la URL del sitio y pruebe el inicio de sesión y los permisos.
8. Cambie todas las contraseñas de demostración y retire `scripts/seed_demo.php` del directorio público una vez terminada la instalación.

La publicación no queda realizada por el solo hecho de tener este código: se debe crear la cuenta del hosting, cargar los archivos y comprobar la URL.

## 9. Repositorio GitHub y espacio Coda

La guía solicita trabajar en grupo, crear un espacio en Coda.io y compartir su enlace con el docente. También solicita un repositorio grupal en GitHub con el formato de ejemplo `github.com/grupo99/pwatarea6` (sustituyendo `99` por el número de grupo asignado).

Pasos:
1. Crear el espacio compartido del grupo en Coda.io y registrar las aportaciones mediante Version History.
2. Confirmar con el docente el nombre exacto del repositorio: en la guía aparece `pwapractica5`, pero el ejemplo de URL utiliza `pwatarea6`. Para evitar entregar una dirección incorrecta, no cree el repositorio hasta confirmar cuál de los dos nombres corresponde.
3. Crear el repositorio con el nombre confirmado dentro de la cuenta u organización del grupo.
4. Subir todos los archivos de este proyecto al repositorio.
5. Copiar la URL real del repositorio y del espacio Coda.
6. Copiar la URL pública del sistema después de publicarlo y probarlo.

No se incluyen enlaces inventados: las direcciones finales deben ser las que genere el grupo al crear sus espacios y publicar el proyecto.

## 10. Respuestas para registrar en EVA

**Respuesta 1 — Espacio colaborativo Coda**

Enlace del espacio de trabajo del grupo: `PEGAR_AQUÍ_EL_ENLACE_REAL_DE_CODA`.

**Respuesta 2 — Repositorio y aplicación publicada**

Repositorio GitHub: `PEGAR_AQUÍ_LA_URL_REAL_DEL_REPOSITORIO`.

Sitio web publicado: `PEGAR_AQUÍ_LA_URL_REAL_DEL_HOSTING`.

## 11. Correspondencia con la rúbrica

- **Comprensión de PHP:** variables, funciones, archivos `include/require`, sesiones, validación y consultas PDO.
- **Comprensión de CSS:** hoja de estilos propia y reglas visuales consistentes.
- **Comprensión de JavaScript:** eventos, confirmaciones, validación y cálculo dinámico del subtotal.
- **Desarrollo PHP:** autenticación, operaciones CRUD, control de permisos y gestión de pedidos.
- **Estilos y diseño:** interfaz adaptable, panel, tarjetas, tablas y formularios con Bootstrap 5 y CSS.

La guía del docente evalúa estos aspectos sobre 100 puntos. La calificación depende de la revisión del profesor y de la demostración funcional del proyecto.

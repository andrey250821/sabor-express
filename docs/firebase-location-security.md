# Firebase: seguimiento de Delivery con reglas permanentes

La aplicación autentica primero al usuario con Laravel. El servidor emite un token personalizado de Firebase para un único pedido después de comprobar que el usuario es su Cliente, el Delivery asignado o un Administrador. El JavaScript utiliza ese token para leer o escribir Realtime Database.

## Configuración inicial (una sola vez)

1. En Firebase Console, abre el proyecto `sabor-express-3e291` y habilita Firebase Authentication desde **Build → Authentication → Get started**.
2. En **Project settings → Service accounts**, genera una nueva clave privada. Descarga el JSON de la cuenta de servicio.
3. En la computadora donde ejecutas Laravel, crea la carpeta `storage/app/firebase` y guarda el archivo descargado con el nombre exacto `sabor-express-service-account.json`.
4. **No subas ese JSON a GitHub ni lo compartas.** La carpeta `storage/app/firebase/` está excluida del control de versiones. Si guardas la credencial en otro lugar, configura `FIREBASE_CREDENTIALS` en tu archivo `.env` con la ruta absoluta.
5. En la carpeta del proyecto, ejecuta:

   ```powershell
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   npm run build
   ```

## Publicar las reglas

Copia el contenido íntegro de `firebase/database.rules.json` en **Realtime Database → Rules** y pulsa **Publish**. Estas reglas no tienen fecha de caducidad: los accesos se validan con los tokens firmados por el servidor y limitados a un pedido.

No vuelvas a publicar las reglas temporales con `.read: true` o `.write: true` para toda la base de datos. El servidor debe tener el archivo de cuenta de servicio para que la autenticación funcione.

## Verificación

- Inicia sesión como el Delivery asignado a un pedido en camino y permite el GPS.
- En Realtime Database → Data, comprueba que `delivery_locations/{idDelPedido}` recibe `latitud`, `longitud` y `actualizado_en`.
- Abre el detalle de ese mismo pedido como Cliente y Administrador y confirma que el marcador se actualiza.
- Intenta acceder a un pedido que no pertenece al usuario: el endpoint de token debe responder HTTP 403.

Si el sistema devuelve el mensaje de que la autenticación de Firebase no está configurada, verifica la ruta y el archivo privado de credenciales. No pongas la clave privada en JavaScript, en una vista Blade ni en el repositorio.

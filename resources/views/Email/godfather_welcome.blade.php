@component('mail::message')
# Bienvenido a la plataforma de Proyecto Sigue

Usted ha sido registrado exitosamente en la aplicacion de Proyecto Sigue.
A continuación le mostramos su correo y su contraseña con la que podrá ingresar
a la aplicación.

<br>

<p> Correo: {{ $auth_info['email'] }} </p>
<p> Contraseña: {{ $auth_info['password'] }} </p>

<br>
{{ config('app.name') }}
@endcomponent

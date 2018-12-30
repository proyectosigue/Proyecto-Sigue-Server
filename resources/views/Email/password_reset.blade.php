@component('mail::message')
# Solicitud de cambio de contraseña

Se ha solicitado un cambio de contraseña para su cuenta en la aplicacion de Proyecto Sigue,
si desea cambiar la contraseña presione el siguiente botón.

@component('mail::button', ['url' => config('app.url'). "/password/reset-form/$token"])
    Cambiar Contraseña
@endcomponent

<br>
{{ config('app.name') }}
@endcomponent

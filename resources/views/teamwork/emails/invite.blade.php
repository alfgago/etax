@component('mail::message')
Hola,
<p>Usted ha sido invitado a unirse a {{$team->name}} en eTax</p>

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="{{ route('teams.accept_invite', $invite->accept_token) }}" class="button button-primary}}">Unirse a equipo</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

Gracias,<br>
{{ config('app.name') }}
@endcomponent

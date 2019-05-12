Hola,
<p>Usted ha sido invitado a utilizar eTax como usuario autorizado de {{$team->name}}. Para empezar, solo debe aceptar la invitación y crear su cuenta.</p>

<table class="action" align="center" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="{{ route('invites.accept_invite', $invite->accept_token) }}" class="button button-primary}}">Aceptar invitación</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

Thanks,<br>
{{ config('app.name') }}

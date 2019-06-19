<div class="main-header">
    <div class="logo">
        <a title="Volver al escritorio" href="/"><img src="{{asset('assets/images/logo-final-150.png')}}" class="logo-img"></a>
    </div>
    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div style="margin: auto"></div>

    <div class="header-part-right">
        
        @if( !empty( auth()->user()->teams ) )
            <div class="companyParent">
                <label for="country">Empresa actual:</label>
                <div class="form-group">
                    <select class="form-control" id="company_change" onchange="companyChange(true);">
                        @foreach( auth()->user()->teams as $row )
                            <?php  $c = $row->company;  ?>
                            <option value="{{ $c->id }}" {{ $c->id == currentCompany() ? 'selected' : ''  }} > {{ $c->name.' '.$c->last_name.' '.$c->last_name2 }} </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        
        <!-- User avatar dropdown -->
        <div class="dropdown">
            <div  class="user col align-self-end">
                <img src="{{asset('assets/images/config-2.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"></i> {{Auth::user()->first_name.' '.Auth::user()->last_name.' '.Auth::user()->last_name2}}
                    </div>
                    <a class="dropdown-item" href="/usuario/perfil">Perfil</a>                     
                    <a class="dropdown-item" href="/empresas/editar">Configuración de empresa</a>   
                    <a class="dropdown-item" href="/payments-methods">Gesti&oacute;n de pagos</a>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                        Cerrar sesión
                    </a>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    function companyChange($redirect = false) {

        var sel = $('#company_change').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "/change-company",
            method: 'post',
            data: {
                companyId: sel
            },
            success: function (result) {
                if ($redirect) {

                    if (typeof is_edit === 'undefined') {
                        window.location.href = window.location.href;
                    } else {
                        window.location.href = result;
                    }
                }

            }});

    }

    $(document).ready(function () {
        companyChange();
    });
</script>


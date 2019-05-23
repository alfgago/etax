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
        
        @if( !empty( userCompanies()->toArray() ) )
        <div class="companyParent">
            <label for="country">Empresa actual:</label>
            <div class="form-group">
                <select class="form-control" id="company_change" onchange="companyChange(true);">
                    @foreach(userCompanies() as $row)
                        <option value="{{$row->id_number}}" {{ $row->company_id == currentCompany() ? 'selected' : ''  }} > {{ $row->name.' '.$row->last_name.' '.$row->last_name2 }} </option>
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
                    <a class="dropdown-item" href="/usuario/cambiar-plan">Cambiar plan</a>
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
        var is_edit = $('#is-company-edit').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        jQuery.ajax({
            url: "{{ url('/change-company') }}",
            method: 'post',
            data: {
                id: sel, is_edit
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


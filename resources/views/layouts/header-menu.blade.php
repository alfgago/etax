<div class="main-header">
    <div class="logo">
        <a title="Volver al escritorio" href="/"><img src="{{asset('assets/images/logo-final-150.png')}}" class="logo-img"></a>
    </div>
    @if( getCurrentSubscription()->status == 4 )
    <div class="comprar-ahora">
        <p class="description">Periodo de uso gratuito</p>
        <a class="btn btn-primary btn-buynow" href="/elegir-plan" title="Comprar ahora">Comprar ahora</a>
    </div>
    @endif
    <div class="menu-toggle">
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div style="margin: auto"></div>

    <div class="header-part-right">
        <style>
.notificaciones-header > a {
    position: relative;
    display: inline-block;
    color: #000;
    margin: 0 1rem;
}

.notificaciones-header .fa {
    position: relative;
    font-size: 1.8rem !important;
    line-height: 2rem;
}

.notificaciones-header .notification-count {
    position: absolute;
    font-size: 8px;
    background: #e00000;
    color: #fff;
    width: 20px;
    height: 20px;
    line-height: 20px;
    border-radius: 50%;
    top: -10px;
    right: -7.5px;
    font-weight: bold;
    text-align: center;
    display: none;
}
.notificaciones-header .mostrar-count-notificacion {
    display: block;
}
.dropdown-notificaciones {
    top: 100%;
    right: 1rem;
    z-index: 1000;
    display: none;
    float: left;
    padding: 1rem 0 0;
    margin: 0.125rem 0 0;
    color: #1e0a26;
    text-align: left;
    list-style: none;
    background-color: #ffffff;
    background-clip: padding-box;
    border-radius: 0.25rem;
    position: absolute;
    min-width: 25rem;   
    border: 0;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
}
.notificacion-nueva{
    background: #c2c2c2;
}
.notificacion-seleccionado{
    background: #e6e6e6;
}
.dropdown-notificaciones h3{
    font-size: 1.2rem;
    margin-bottom: 1.25rem;
    margin-left: 20px;
    border-bottom: 0.45rem solid #F0C962;
    display: inline-block;
}
.divicion-notificaciones{
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    border: 0;
    border-top: 0.1rem dashed #e5e5e5;
    height: 0;
    border-top-style: dashed;
}
.date-notificaciones{
    font-size: 10px;
}
.notificacion-info{
    color: #2F96B4;
}
.notificacion-success{
    color: #51A351;
}
.notificacion-error{
    color: #BD362F;
}
.notificacion-warning{
    color: #F89406;
}
.ver-mas-notificaciones {
    text-align: center;
    text-transform: uppercase;
    width: 100%;
    display: block;
    border-top: 2px solid;
    padding-top: 5px;
    color: #ffffff;
    background: #15408E;
}
.div-notificaciones-left{
    height: calc(100vh - 21rem);
    overflow-y: auto;
}
.div-notificaciones-rigth{
    height: calc(100vh - 21rem);
    overflow-y: auto;
}
.icono-notificaciones i {
    padding-right: 5px;
}
.titulo-notificacion .icono-notificaciones i {
    padding-right: 5px;
    font-size: 2rem !important;
}
.date-notificacion{
    font-size: 10px;
}
#imprimir-notificaciones{
    overflow-y:auto;
    max-height:20rem;
    transition: ease;
    transition-duration: 0.8s;
}
.todas-notificaciones .ver-mas-notificaciones{
    display: none;
}
.todas-notificaciones #imprimir-notificaciones{
    overflow-y:auto;
    max-height:40rem;
    transition: ease;
    transition-duration: 0.8s;
}
.todas-notificaciones{
    min-width: 80rem;
    transition: ease;
    transition-duration: 0.8s;
}
.cerrar-notificaciones{
    color: #d82f2f;
    top: 1rem;
    right: 5px;
    position: absolute;
    cursor: pointer;
}
        </style>
        @if( !empty( auth()->user()->teams ) )
            <div class="companyParent">
                <label for="country">Empresa actual:</label>
                <div class="form-group">
                    <select class="form-control select-search" id="company_change" onchange="companyChange(true);">

                        @foreach( auth()->user()->teams as $row )
                            <?php  
                                  $c = $row->company;
                                  if($c) { 
                                    if($c->status == 1){
                                    $name = isset($c->name) ? $c->name.' '.$c->last_name.' '.$c->last_name2 : '-- Nueva Empresa --';  ?> 
                                    <option value="{{ $c->id }}" {{ $c->id == currentCompany() ? 'selected' : ''  }} > {{ $name }} </option>
                            <?php   } 
                                  } ?>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        <div class="notificaciones-header"> 
            <a href="#" id="notificacionesDropdown">
                <span class="fa fa-bell-o" aria-hidden="true"></span>
                <span class="notification-count @if(notification_count() != 0) mostrar-count-notificacion @endif ">{{notification_count()}}</span>
            </a>
            <div class="dropdown-notificaciones dropdown-notificaciones-right" >
                <h3 class="text-center">Notificaciones</h3>
                <span class="cerrar-notificaciones"><i class="fa fa-times" aria-hidden="true"></i></span>
                <div id="imprimir-notificaciones" ></div>
                <div class="ver-mas-notificaciones" onclick="verNotificacion(0);">
                    Ver todas
                </div>
            </div>
        </div>
        <!-- User avatar dropdown -->
        <div class="dropdown">
            <div  class="user col align-self-end">
                <img src="{{asset('assets/images/config-2.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"></i> {{Auth::user()->first_name.' '.Auth::user()->last_name.' '.Auth::user()->last_name2}}
                    </div>
                    <?php 
                    $menu = new App\Menu;
                    $items = $menu->menu('menu_dropdown_header');
                    foreach ($items as $item) { ?>
                        <a class="dropdown-item" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
                    <?php } ?>
                        <a class="dropdown-item" onclick="event.preventDefault(); document.getElementById(&quot;frm-logout&quot;).submit();">Cerrar sesión</a>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    setInterval('contadorNotificaciones()',30000);
    $(".cerrar-notificaciones").click(function(){
        $("#notificacionesDropdown").click();
    });
    $("#notificacionesDropdown").click(function(){
        var link = "/usuario/notificaciones-nuevas";
        console.log(link);
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $(".dropdown-notificaciones").removeClass("todas-notificaciones");
              $("#imprimir-notificaciones").html(data);
           }
      
        });
        $(".dropdown-notificaciones").slideToggle("slow");
    });
    function cerrarNotificaciones(){
        $(".dropdown-notificaciones").removeClass("todas-notificaciones");
    }
    function notificacionVista(id){
        var link = "/usuario/notificaciones-vista/"+id;
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                if(data == 1){
                    $(".div-notificacion-"+id).removeClass("notificacion-nueva");
                }
           }
      
        });
    }

    function verNotificacion(id){

        var link = "/usuario/notificaciones/"+id;
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $("#imprimir-notificaciones").html(data);
                $(".dropdown-notificaciones").addClass("todas-notificaciones");
           }
      
        });
    }

    function contadorNotificaciones(){
        var link = "/usuario/notificaciones-contador";
        $.ajax({
           type:'GET',
           url:link,
           success:function(data){
                $(".notification-count").html(data);
                if(data == 0){
                    $(".notification-count").removeClass("mostrar-count-notificacion");
                }else{
                    $(".notification-count").addClass("mostrar-count-notificacion");
                }
            }
        });
    }

</script>


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
                    
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    
</script>


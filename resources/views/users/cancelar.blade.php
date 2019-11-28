@extends('layouts/app')

@section('title')
    Cancelar subscripción
@endsection

@section('breadcrumb-buttons')
@endsection 

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                         <?php 
                        $menu = new App\Menu;
                        $items = $menu->menu('menu_perfil');
                        foreach ($items as $item) { ?>
                            <li>
                                <a class="nav-link @if($item->link == '/usuario/empresas') active @endif" aria-selected="false"  style="color: #ffffff;" {{$item->type}}="{{$item->link}}">{{$item->name}}</a>
                            </li>
                        <?php } ?>
                        @if( auth()->user()->isContador() )
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                        @endif
                        @if( auth()->user()->isInfluencers())
                         <li style="display:none;">
                                <a class="nav-link" aria-selected="false" href="/usuario/wallet">Billetera</a>
                           </li>
                        @endif
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       

                        <div class="tab-pane fade show active" role="tabpanel">
                            <form method="POST" action="/usuario/update-cancelar">

                                @csrf
                                @method('patch') 

                                <div class="form-row">

                                    <div class="form-group col-md-12">
                                      <label for="motivo">Motivo de la cancelación del plan</label>
                                      <textarea class="form-control" name="motivo" id="motivo" ></textarea>
                                    </div>
                                    
                                    <button id="btn-submit" type="submit" class="btn btn-danger">Cancelar</button>
                                    
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection


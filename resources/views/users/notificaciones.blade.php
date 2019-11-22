@extends('layouts/app')

@section('title')
Notificaciones
@endsection

@section('breadcrumb-buttons')
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        
        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3 div-notificaciones-left">
                    <ul >
                        @foreach($notificaciones as $notification) 
                            <a href="/usuario/notificaciones/{{$notification->id}}" >
                            <div class="col-md-12 div-notificaciones div-notificacion-{{$notification->id}} @if($notification->status == 1) notificacion-nueva @endif @if($id == $notification->id) notificacion-seleccionado @endif ">
                                <span class="titulo-notificaciones">{!!$notification->notification->icon()!!} {{$notification->notification->title}}</span></br>
                                <span class="date-notificaciones">{{$notification->notification->date}}</span></br>
                            </div>
                        </a> 
                        <hr class="divicion-notificaciones">
                        @endforeach
                    </ul>
                        
                </div>
                <div class="col-9 div-notificaciones-rigth">
                    @if($notificacionAbierta)
                        <h1 class="titulo-notificacion">{!!$notificacionAbierta->notification->icon()!!} {{$notificacionAbierta->notification->title}}</h1>
                        <span class="date-notificacion">{{$notificacionAbierta->notification->date}}</span><br>
                        <span class="text-notificacion">{!!$notificacionAbierta->notification->text!!}</span><br>
                        @if($notificacionAbierta->notification->link != '')
                            <span class="link-notificacion"><a target="_blank" href="{{$notificacionAbierta->notification->link}}">Ver enlace</a></span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>  
</div>       

@endsection

@section('footer-scripts')
<script>
    $(".div-notificaciones-left").animate({
        scrollTop: $(".div-notificacion-{{$id}}").offset().top - 300
    }, 500);
</script>
@endsection


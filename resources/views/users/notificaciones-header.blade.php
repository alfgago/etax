

@foreach(notifications() as $notification)
    <a href="/usuario/notificaciones/{{$notification->id}}" class="a-notificacion-{{$notification->id}} ">
        <div class="col-md-12 div-notificaciones ">
            <span class="titulo-notificaciones">{!!$notification->notification->icon()!!} {{$notification->notification->title}}</span></br>
            <span class="date-notificaciones">{{$notification->notification->date}}</span></br>
        </div>
    </a> 
    <hr class="divicion-notificaciones">
@endforeach

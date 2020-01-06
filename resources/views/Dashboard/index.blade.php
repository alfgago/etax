@extends('layouts/app') 

@section('title') 
    Escritorio
@endsection 

@section('header-scripts')

<script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
<script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>

@endsection

@section('breadcrumb-buttons')
<div class="mobile-flex">
  <div class="periodo-actual filters">
    <form class="periodo-form">
      <?php 
        $mes = \Carbon\Carbon::now()->month;
        $ano = \Carbon\Carbon::now()->year;
      ?>
      <label>Filtrar por fecha</label>
      <div class="periodo-selects">
        <select id="input-ano" name="input-ano" onchange="loadReportes();">
            <option value="2019">2019</option>
            <option selected value="2020">2020</option>
        </select>
        <select id="input-mes" name="input-mes" onchange="loadReportes();">
            <option value="1" {{ $mes == 1 ? 'selected' : ''  }}>Enero</option>
            <option value="2" {{ $mes == 2 ? 'selected' : ''  }}>Febrero</option>
            <option value="3" {{ $mes == 3 ? 'selected' : ''  }}>Marzo</option>
            <option value="4" {{ $mes == 4 ? 'selected' : ''  }}>Abril</option>
            <option value="5" {{ $mes == 5 ? 'selected' : ''  }}>Mayo</option>
            <option value="6" {{ $mes == 6 ? 'selected' : ''  }}>Junio</option>
            <option value="7" {{ $mes == 7 ? 'selected' : ''  }}>Julio</option>
            <option value="8" {{ $mes == 8 ? 'selected' : ''  }}>Agosto</option>
            <option value="9" {{ $mes == 9 ? 'selected' : ''  }}>Setiembre</option>
            <option value="10" {{ $mes == 10 ? 'selected' : ''  }}>Octubre</option>
            <option value="11" {{ $mes == 11 ? 'selected' : ''  }}>Noviembre</option>
            <option value="12" {{ $mes == 12 ? 'selected' : ''  }}>Diciembre</option>
        </select>
      </div>
    </form>
  </div>

<?php /*
  $permisos = new App\User;
dd($permisos->permisos());

  ?>
@if(in_array(1, $permisos->permisos()) || in_array(6, $permisos->permisos()) || in_array(8, $permisos->permisos()))     
*/
?>
@if($mostrar_dashboard == 1)
  <div class="toggle-vista filters hidden">
    <label id="vistabasica">Vista de dashboard</label>
    <div class="">
      <select id="input-vista" name="input-vista" onchange="loadReportes();">
        <option value="basica" selected > Vista básica</option>
        <option value="gerencial"> Vista avanzada</option>
      </select>
    </div>
  </div>
  
@endif    
  
</div>
@endsection 

@section('content')

<div class="row" id="reporte-container">
  
  
</div>

@endsection 

@section('footer-scripts')

<script>
  function loadReportes() {
    var vista = $("#input-vista").val();
    var mes = $("#input-mes").val();
    var ano = $("#input-ano").val();
      		  
    jQuery.ajax({
      url: "/reportes/reporte-dashboard",
      type: 'post',
      cache: false,
      data : {
        mes : mes,
  		  ano : ano,
  		  vista : vista,
  		  _token: '{{ csrf_token() }}'
      },
      success : function( response ) {
        $('#reporte-container').html(response);
        initHelpers();
      },
      async: true
    });  
  }
    
  $( document ).ready(function() {  
    loadReportes();
  });



</script>


@endsection

@extends('layouts/app')

@section('title') 
  Facturas emitidas
@endsection

@section('breadcrumb-buttons')
    <button type="submit" onclick="$('#btn-submit-form').click();" class="btn btn-primary">Validar Líneas</button>
@endsection 

@section('content') 
<div class="row">
  <div class="col-md-12">
    <label>Filtrar documentos</label>
      <div class="filters mb-4 pb-4 row">
          <div class="periodo-selects">
            <select id="filtro-select-tarifa" name="filtro" onchange="reloadDataTableTarifa();">
                <option value="99" selected>Todas las tarifas</option>
                <option value="10">0%</option>
                <option value="1">1%</option>
                <option value="2">2%</option>
                <option value="4">4%</option>
                <option style="display:none;" value="8">8%</option>
                <option value="13">13%</option>
            </select>
          </div>
          <div class="periodo-selects">
            <select id="filtro-select-codificadas" name="filtro-validado" onchange="reloadDataTable();">
                <option value="99">Todas las líneas</option>
                <option value="1" selected>Líneas por validar</option>
                <option value="2">Líneas ya validadas</option>
                <option value="3">Líneas en facturas no validadas</option>
            </select>
          </div>
          <div class="periodo-selects">
            <select id="filtro-select-mes" name="filtro-validado" onchange="reloadDataTable();">
                <option style="display:none;" value="0" >Todos los meses</option>
                <option value="1" {{date('n') == 1 ? 'selected' : ''}}>Enero</option>
                <option value="2" {{date('n') == 2 ? 'selected' : ''}}>Febrero</option>
                <option value="3" {{date('n') == 3 ? 'selected' : ''}}>Marzo</option>
                <option value="4" {{date('n') == 4 ? 'selected' : ''}}>Abril</option>
                <option value="5" {{date('n') == 5 ? 'selected' : ''}}>Mayo</option>
                <option value="6" {{date('n') == 6 ? 'selected' : ''}}>Junio</option>
                <option value="7" {{date('n') == 7 ? 'selected' : ''}}>Julio</option>
                <option value="8" {{date('n') == 8 ? 'selected' : ''}}>Agosto</option>
                <option value="9" {{date('n') == 9 ? 'selected' : ''}}>Setiembre</option>
                <option value="10" {{date('n') == 10 ? 'selected' : ''}}>Octubre</option>
                <option value="11" {{date('n') == 11 ? 'selected' : ''}}>Noviembre</option>
                <option value="12" {{date('n') == 12 ? 'selected' : ''}}>Diciembre</option>
            </select>
          </div>
          <div class="periodo-selects">
            <select id="filtro-select-unidad" name="filtro-validado" onchange="reloadDataTable();">
                <option value="" selected>Todas las unidades</option>
                @foreach ( $unidades as $unidad )
                  <option value="{{$unidad->measure_unit}}" >{{$unidad->measure_unit}}</option>

                @endforeach
            </select>
          </div>
      </div>
    <form method="POST" action="/facturas-emitidas/validacion-masiva" class="show-form btn-submit-form">
      @csrf
      @method('post') 
      <table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="3">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th data-priority="5">Unidad</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="5">Total</th>
              <th data-priority="5">Tarifa IVA</th>
              <th data-priority="3">Código eTax</th>
              <th data-priority="3">Categoría Hacienda</th>
            </tr>
          </thead>
          <thead>
            <tr>
               <th colspan="7">Selección masiva: </th>
               <td>
                  <div class="">
                    <select class="form-control iva_type_all"  placeholder="Seleccione un código eTax"  >
                        <option value="0" porcentaje="">-- Seleccione --</option>
                        <?php
                          $preselectos = array();
                          foreach($company->repercutidos as $repercutido){
                            $preselectos[] = $repercutido->id;
                          }
                        ?>
                        @if(@$company->repercutidos[0]->id)
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                              <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select_all" {{ (in_array($tipo['id'], $preselectos) == false) ? 'hidden preselect=0' : 'preselect=1' }}  >{{ $tipo['name'] }}</option>
                          @endforeach
                          <option class="mostrarTodos_all" porcentaje="" preselect="1" value="1">Mostrar Todos</option>
                        @else
                          @foreach ( \App\CodigoIvaRepercutido::where('hidden', false)->get() as $tipo )
                          <option value="{{ $tipo['code'] }}" porcentaje="{{ $tipo['percentage'] }}" class="tipo_iva_select_all"  preselect="1">{{ $tipo['name'] }}</option>
                          @endforeach
                        @endif
          
                    </select>
                  </div>
               </td>
               <td>
                 <div class="">
                   <select class="form-control product_type_all"  placeholder="Seleccione una categoría de hacienda" >
                      <option value="0" posibles="">-- Seleccione --</option>
                      @foreach($categoriaProductos as $cat)
                         <option value="{{@$cat->id}}" codigo="{{ @$cat->invoice_iva_code }}" posibles="{{@$cat->open_codes}}" >{{@$cat->name}}</option>
                      @endforeach
                    </select>
                  </div>
               </td>
             </tr>

          </thead>
          <tbody>
          </tbody>
        </table>
        <div class="btn-holder hidden">
            <button id="btn-submit-form" type="submit" class="btn btn-primary">Guardar factura</button>
          </div>
      </form>
  </div>  
</div>

@endsection

@section('footer-scripts')

<script>
  
var datatable;
$(function() {
  datatable = $('#invoice-table').DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [ 100, 500, 1000, 2000],
    pageLength: 1000,
    ajax: {
      url: "/api/invoices-masivo",
      data: function(d){
          d.filtroTarifa = $( '#filtro-select-tarifa' ).val();
          d.filtroValidado = $( '#filtro-select-codificadas' ).val();
          d.filtroUnidad = $( '#filtro-select-unidad' ).val();
          d.filtroMes = $( '#filtro-select-mes' ).val();
          
      },
      type: 'GET'
    },
    order: [[ 1, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'invoices.document_number' },
      { data: 'client', name: 'invoices.client_first_name'},
      { data: 'unidad', name: 'invoice_items.measure_unit'},
      { data: 'subtotal', name: 'invoice_items.subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'iva_amount', name: 'invoice_items.iva_amount', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'total', name: 'invoice_items.total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'tarifa_iva', name: 'invoice_items.tarifa_iva', orderable: false, searchable: false },
      { data: 'codigo_etax', name: 'codigo_etax', orderable: false, searchable: false },
      { data: 'categoria_hacienda', name: 'categoria_hacienda', orderable: false, searchable: false },
    ],
    createdRow: function (row, data, index) {
      if(data.hide_from_taxes){
        $(row).addClass("tax-hidden");
      }
    },
    rowCallback: function (row, data) {
        var element = $(row).find('.iva_type');
        element.on("change", function () {
            if($(this).val() == 1){
               $.each($('.tipo_iva_select'), function (index, value) {
                $(value).removeClass("hidden");
              })
               $('.mostrarTodos').addClass("hidden");
               $('.iva_type').val("");
            }
            var iva_type  = $(this).val(); 
            var parent = $(this).parents('tr');
            parent.find('.product_type option').hide();
            var tipoProducto = 0;
            parent.find(".product_type option").each(function(){
              var posibles = $(this).attr('posibles').split(",");
              if(posibles.includes(iva_type)){
                $(this).show();
                if( !tipoProducto ){
                  tipoProducto = $(this).val();
                }
              }
            });
            parent.find('.product_type').val( tipoProducto ).change();
        });
        element.change();
    },
    language: {
      url: "/lang/datatables-es_ES.json",
    }
  });
});

function reloadDataTable() {
  datatable.ajax.reload();
}

function reloadDataTableTarifa() {
  
  var filtroTarifa = $( '#filtro-select-tarifa' ).val();
  if(filtroTarifa != 99){
    if(filtroTarifa == 10){
      filtroTarifa = 0;
    }
    var parent = $(".iva_type_all")
    parent.find('option').hide();
    parent.find("option").each(function(){
    var porcentaje = $(this).attr('porcentaje');
      if(porcentaje == filtroTarifa){
        $(this).show();
      }
    });
  }else{
    var parent = $(".iva_type_all")
    parent.find("option").each(function(){
        $(this).show();
    });    
  }
  reloadDataTable();
}


$(document).ready(function(){

    $(".product_type_all").change(function(){
        var product_type  = $(this).val(); 
        if(product_type != 0){
        $(".product_type").val(product_type);
      }
    });
    $(".iva_type_all").change(function(){
        var iva_type  = $(this).val(); 
        if(iva_type != 0 && iva_type != 1){
          $(".iva_type").each(function(){
            $(this).val(iva_type);
            $(this).change();
          });
        }
        var parent = $(this).parents('tr');
        parent.find('.product_type_all option').hide();
        var tipoProducto = 0;
        parent.find(".product_type_all option").each(function(){
          var posibles = $(this).attr('posibles').split(",");
          if(posibles.includes(iva_type)){
            $(this).show();
            if( !tipoProducto ){
              tipoProducto = $(this).val();
            }
          }
        });
        parent.find('.product_type_all').val( tipoProducto ).change();

    });
    $(".iva_type").change(function(){
      var iva_type  = $(this).val(); 
      var parent = $(this).parents('tr');
      console.log(parent);
      parent.find('.product_type option').hide();
      var tipoProducto = 0;
      parent.find(".product_type option").each(function(){
        var posibles = $(this).attr('posibles').split(",");
        if(posibles.includes(iva_type)){
          $(this).show();
          if( !tipoProducto ){
            tipoProducto = $(this).val();
          }
        }
      });
      parent.find('.product_type').val( tipoProducto ).change();

      });
      $('.iva_type').change();
      $(".product_type").each(function(){
        $(this).val($(this).attr('curr')).change();
      })
});
  
  
    $( document ).ready(function() {

      $(".iva_type_all").change(function(){
        var filtroTarifa = $( '#filtro-select-tarifa' ).val();
        if($(this).val() == 1){
           $.each($('.tipo_iva_select_all'), function (index, value) { 
           var porcentaje = $(this).attr('porcentaje');
           if(filtroTarifa != 99){
            if(filtroTarifa == 10){
              filtroTarifa = 0;
            }
              if(porcentaje == filtroTarifa){
                $(this).removeAttr("hidden");
                $(this).show();
                $('.mostrarTodos_all').addClass("hidden");
                $('.iva_type_all').val(0);
              }
           }else{
              $(this).removeAttr("hidden");
              $(this).show();
              $('.mostrarTodos_all').addClass("hidden");
              $('.iva_type_all').val(0);
           }      
          })
        }
      });     
    }); 

  
</script>
<style>
  table.table-bordered.dataTable th, table.table-bordered.dataTable td {
    border-width: 0;
  }
  .pb-4 {
    margin-left: 0px!important;
    padding-bottom: 1.5rem !important;
}

</style>
<style>
  tr.tax-hidden td {
    text-decoration: line-through !important;
  }
</style>

@endsection

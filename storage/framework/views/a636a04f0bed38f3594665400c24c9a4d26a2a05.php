<?php $__env->startSection('title'); ?> 
  Facturas recibidas
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb-buttons'); ?>
    <button type="submit" onclick="$('#btn-submit-form').click();" class="btn btn-primary">Validar Líneas</button>
<?php $__env->stopSection(); ?> 

<?php $__env->startSection('content'); ?> 
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
                <option value="99" >Todas las líneas</option>
                <option value="1" selected>Líneas por validar</option>
                <option value="2">Líneas ya validadas</option>
                <option value="3">Líneas en facturas no validadas</option>
            </select>
          </div>
          <div class="periodo-selects">
            <select id="filtro-select-mes" name="filtro-validado" onchange="reloadDataTable();">
            	<!--<?php echo e(date('n') == 1 ? 'selected' : ''); ?> -->
                <option value="0" selected>Todos los meses</option>
                <option value="1">Enero</option>
                <option value="2">Febrero</option>
                <option value="3">Marzo</option>
                <option value="4">Abril</option>
                <option value="5">Mayo</option>
                <option value="6">Junio</option>
                <option value="7">Julio</option>
                <option value="8">Agosto</option>
                <option value="9">Setiembre</option>
                <option value="10">Octubre</option>
                <option value="11">Noviembre</option>
                <option value="12">Diciembre</option>
            </select>
          </div>
          <div class="periodo-selects">
            <select id="filtro-select-ano" name="filtro-validado" onchange="reloadDataTable();">
            	<option value="" selected>Todas los años</option>
                <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                	<option value="<?php echo e($year->year); ?>"><?php echo e($year->year); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            </select>
          </div>

          <div class="periodo-selects">
            <select id="filtro-select-unidad" name="filtro-validado" onchange="reloadDataTable();">
                <option value="" selected>Todas las unidades</option>
                <?php $__currentLoopData = $unidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($unidad->measure_unit); ?>" ><?php echo e($unidad->measure_unit); ?></option>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="periodo-selects">
            <select id="filtro-actividad" name="filtro-actividad" onchange="reloadDataTable();">
                <option value="" selected>Todas las actividades</option>
               	<?php $__currentLoopData = $commercial_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commercial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e(@$commercial->codigo); ?>"><?php echo e(@$commercial->actividad); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
      </div>
    <form method="POST" action="/facturas-recibidas/validacion-masiva" class="show-form btn-submit-form">
      <?php echo csrf_field(); ?>
      <?php echo method_field('post'); ?> 
      <table id="bill-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="4">Comprobante</th>
              <th data-priority="4">Proveedor</th>
              <th data-priority="5">Unidad</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="5">Total</th>
              <th data-priority="5">Tarifa IVA</th>
              <th data-priority="3">Código eTax</th>
              <th data-priority="3">Categoría Hacienda</th>
              <th data-priority="6">Identificación plena</th>
              <!--th data-priority="4">Acciones</th-->
            </tr>
          </thead>
          <thead>
            <tr>
               <th colspan="1">Actividad Comercial: </th>
               <th colspan="2">
                  <select name="actividad_comercial" class="form-control"  placeholder="Seleccione actividad comercial">
                 	<?php $__currentLoopData = $commercial_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commercial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e(@$commercial->codigo); ?>"><?php echo e(@$commercial->actividad); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
               </th>
               <th colspan="4">Selección masiva: </th>
               <td>
                  <div class="">
                    <select class="form-control iva_type_all"  placeholder="Seleccione un código eTax"  >
                        <option value="0" porcentaje="101" preselect=1>-- Seleccione --</option>
                        <?php
                          $preselectos = array();
                          foreach($company->soportados as $soportado){
                            $preselectos[] = $soportado->id;
                          }
                        ?>
                        <?php if(@$company->soportados[0]->id): ?>
                          <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select_all" <?php echo e((in_array($tipo['id'], $preselectos) == false) ? 'hidden preselect=0' : 'preselect=1'); ?>  ><?php echo e($tipo['name']); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          <option class="mostrarTodos_all" porcentaje="101" preselect="1" value="1">Mostrar Todos</option>
                        <?php else: ?>
                          <?php $__currentLoopData = \App\CodigoIvaSoportado::where('hidden', false)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($tipo['code']); ?>" porcentaje="<?php echo e($tipo['percentage']); ?>" class="tipo_iva_select_all" preselect="1"><?php echo e($tipo['name']); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
          
                    </select>
                  </div>
               </td>
               <td>
                 <div class="">
                   <select class="form-control product_type_all"  placeholder="Seleccione una categoría de hacienda" >
                      <option value="0" posibles="">-- Seleccione --</option>
                      <?php $__currentLoopData = $categoriaProductos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <option style="display: none;" value="<?php echo e(@$cat->id); ?>" codigo="<?php echo e(@$cat->invoice_iva_code); ?>" posibles="<?php echo e(@$cat->open_codes); ?>" ><?php echo e(@$cat->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <option value="0" posibles="" style="bakground: #eee"> -- Seleccionar código antes de elegir categoría  --</option>
                    </select>
                  </div>
               </td>
               <td>
                  <div class="">
                   <select class="form-control identificacion_especifica_all"  placeholder="Seleccione una categoría de hacienda" >
                      <option value="0" posibles="">-- Seleccione --</option>
                      <option value="10" posibles="">0%</option>
                      <option value="1" posibles="">1%</option>
                      <option value="2" posibles="">2%</option>
                      <option value="4" posibles="">4%</option>
                      <option value="13" posibles="">13%</option>
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-scripts'); ?>

<script>
  
var datatable;
$(function() {
  datatable = $('#bill-table').DataTable({
    processing: true,
    serverSide: true,
    lengthMenu: [ 100, 500, 1000, 2000],
    pageLength: 1000,
    ajax: {
      url: "/api/bills-masivo",
      data: function(d){
          d.filtroTarifa = $( '#filtro-select-tarifa' ).val();
          d.filtroValidado = $( '#filtro-select-codificadas' ).val();
          d.filtroUnidad = $( '#filtro-select-unidad' ).val();
          d.filtroMes = $( '#filtro-select-mes' ).val();
          d.filtroAno = $( '#filtro-select-ano' ).val();       
          d.filtroActividad = $( '#filtro-actividad' ).val();             
      },
      type: 'GET'
    },
    order: [[ 1, 'desc' ]],
    columns: [
      { data: 'document_number', name: 'bills.document_number' },
      { data: 'client', name: 'bills.provider_first_name' },
      { data: 'unidad', name: 'bill_items.measure_unit'},
      { data: 'subtotal', name: 'bill_items.subtotal', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'monto_iva', name: 'bill_items.iva_amount', class: "text-right" },
      { data: 'total', name: 'bill_items.total', 'render': $.fn.dataTable.render.number( ',', '.', 2 ), class: "text-right" },
      { data: 'tarifa_iva', name: 'bill_items.tarifa_iva', orderable: false, searchable: false },
      { data: 'codigo_etax', name: 'codigo_etax', orderable: false, searchable: false },
      { data: 'categoria_hacienda', name: 'categoria_hacienda', orderable: false, searchable: false },
      { data: 'identificacion_especifica', name: 'identificacion_especifica', orderable: false, searchable: false },
      //{ data: 'actions', name: 'actions', orderable: false, searchable: false }, //Queda documentado pero con desbloquear esto y arriba la tabla se podria rechazar desde esta parte tambien.
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
               //$('.iva_type').val("");
            }
            var iva_type  = $(this).val(); 
            var identificacion = $(this).find(':selected').attr('is_identificacion_plena');
            var parent = $(this).parents('tr');
            if(identificacion == 1){
                parent.find(".porc_identificacion_plena").removeClass("hidden");
                parent.find(".porc_identificacion_plena").attr("required");
            }else{
                parent.find(".porc_identificacion_plena").addClass("hidden");
                parent.find(".porc_identificacion_plena").removeAttr("required");
            }
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
    var preselect = $(this).attr('preselect');
      if((porcentaje == filtroTarifa && preselect == 1) || porcentaje == 101){
        $(this).removeAttr("hidden");
        $(this).show();
      }
      if($(this).val().indexOf("S097") >= 0 || $(this).val().indexOf("B097") >= 0  ){
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


function confirmDecline( id ) {
  $.ajax({
      url: "/api/oneBill/"+id,
      dataType: 'json',
      type: 'GET',
      success: function(bill){
        console.log(bill);
        var formId = "#decline-form-"+id;
        Swal.fire({
          title: '¿Está seguro que desea rechazar la factura a la que pertenece esta linea?',
          html: '<table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"><thead><tr>'+
              '<th>Consecutivo</th>'+
              '<th>Receptor</th>'+
              '<th>Total Factura</th>'+
              '<th>F. Generada</th>'+
              '</tr></thead><tbody><tr>'+
              '<th>'+ bill["document_number"] +'</th>'+
              '<th>'+ bill["provider_first_name"] +'</th>'+
              '<th>'+ bill["total"] +'</th>'+
              '<th>'+ bill["generated_date"] +'</th>'+
              '</tr></tbody>',
          type: 'warning',
          showCloseButton: true,
          showCancelButton: true,
          confirmButtonText: 'Sí, quiero eliminarla'
        }).then((result) => {
          if (result.value) {
            $(formId).submit();
          }
        })
      }
  });
}

$(document).ready(function(){

    $(".product_type_all").change(function(){
        var product_type  = $(this).val(); 
        if(product_type != 0){
        $(".product_type").val(product_type);
      }
    });
    $(".identificacion_especifica_all").change(function(){
        var identificacion  = $(this).val(); 
        if(identificacion != 0){
        $(".porc_identificacion_plena").val(identificacion);
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
    });
      $('.iva_type').change();
      $(".product_type").each(function(){
        $(this).val($(this).attr('curr')).change();
      });
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

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Bill/index-masivo.blade.php ENDPATH**/ ?>
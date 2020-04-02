@extends('layouts/app')

@section('title') 
    Comparativo de clientes - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12">
        
        <form method="POST" action="/quickbooks/clientes/sync-qbaetax" class="form-row">
            @csrf
          
            <div class="form-group col-md-12">  
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                        Clientes Quickbooks
                        </h3>
                    </div>
                    
                    <div class="form-group col-md-12 periodo-actual filters">
                      <div class="div-filtro hidden">
                        <label>Filtrar por fecha</label>
                        <div>
                        <select id="input-ano" name="year" onchange="reloadComparativo();">
                            <option {{ $year == 2019 ? 'selected' : ''  }} value="2019">2019</option>
                            <option {{ $year == 2020 ? 'selected' : ''  }} value="2020">2020</option>
                        </select>
                        <select id="input-mes" name="month" onchange="reloadComparativo();">
                            <option value="1" {{ $month == 1 ? 'selected' : ''  }}>Enero</option>
                            <option value="2" {{ $month == 2 ? 'selected' : ''  }}>Febrero</option>
                            <option value="3" {{ $month == 3 ? 'selected' : ''  }}>Marzo</option>
                            <option value="4" {{ $month == 4 ? 'selected' : ''  }}>Abril</option>
                            <option value="5" {{ $month == 5 ? 'selected' : ''  }}>Mayo</option>
                            <option value="6" {{ $month == 6 ? 'selected' : ''  }}>Junio</option>
                            <option value="7" {{ $month == 7 ? 'selected' : ''  }}>Julio</option>
                            <option value="8" {{ $month == 8 ? 'selected' : ''  }}>Agosto</option>
                            <option value="9" {{ $month == 9 ? 'selected' : ''  }}>Setiembre</option>
                            <option value="10" {{ $month == 10 ? 'selected' : ''  }}>Octubre</option>
                            <option value="11" {{ $month == 11 ? 'selected' : ''  }}>Noviembre</option>
                            <option value="12" {{ $month == 12 ? 'selected' : ''  }}>Diciembre</option>
                        </select>
                        </div>
                      </div>
                      <div class="div-filtro">
                        <label>Origen de sincronización:</label>
                        <div>
                        <select id="input-tipo" name="tipo" onchange="reloadComparativo();">
                            <option value="qbaetax" selected>Desde QuickBooks</option>
                            <option value="etaxaqb">Desde eTax</option>
                        </select>
                        </div>
                      </div>
                      <div class="div-filtro">
                        <label>Refrescar datos</label>
                        <div>
                        <a type="button" class="btn btn-success inline-button" href="#" onclick="resyncQuickbooks();">
                            <i class="fa fa-refresh" aria-hidden="true"></i> <span class="">Re-sincronizar datos de Quickbooks</span>
                        </a>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered comparativa" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Equivalente en eTax</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <button id="btn-submit" type="submit" class="hidden">Guardar clientes</button>
            
        </form>
         
        
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar</button>
@endsection 

@section('footer-scripts')

<script>
    var datatable;
    $(function() {
      datatable = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: "/quickbooks/clientes/data-qbaetax",
          type: 'GET'
        },
        order: [[ 1, 'asc' ]],
        columns: [
          { data: 'link', name: 'link' },
          { data: 'full_name', name: 'full_name' },
          { data: 'email', name: 'email' },
          { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
          url: "/lang/datatables-es_ES.json",
        },
        "drawCallback": function(settings, json) {
            $('.select-search').select2({
                templateResult: function(data, container) {
                  if (data.element) {
                    $(container).addClass($(data.element).attr("class"));
                  }
                  return data.text;
                }
            });
        }
      });
    });
    
    function reloadComparativo() {
        var mes = $("#input-mes").val();
        var ano = $("#input-ano").val();
        var tipo = $("#input-tipo").val();
        if(tipo == 'etaxaqb'){
          window.location.replace("/quickbooks/clientes/comparativo-etaxaqb");
        }else{
          window.location.replace("/quickbooks/clientes/comparativo");
        }
    }

    function resyncQuickbooks() {
        Swal.fire({
            title: '¿Está seguro que desea re-sincronizar con Quickbooks?',
            text: "Este cargará todos los clientes de Quickbooks nuevamente. Puede tardar unos minutos. No es necesario hacerlo si no ha agregado clientes recientemente desde el panel de Quickbooks.",
            type: 'info',
            customContainerClass: 'container-success',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: 'Sí, quiero sincronizar',
          }).then((result) => {
            if (result.value) {
                /*var mes = $("#input-mes").val();
                var ano = $("#input-ano").val();
                window.location.replace("/quickbooks/resync-clients/"+ano+"/"+mes);*/
                window.location.replace("/quickbooks/clientes/resync/");
            }
        })
        
    }
    function sendQbaetax( id ) {
      
      var formId = "#qbaetax-form-"+id;
      Swal.fire({
        title: '¿Está seguro que desea copiar la factura a eTax?',
        text: "La factura será clonada en eTax utilizando por defecto el mapeo de variables en su cuenta.",
        type: 'info',
        customContainerClass: 'container-success',
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: 'Sí, quiero copiarla',
      }).then((result) => {
        if (result.value) {
          $(formId).submit();
        }
      })
      
    }
    
</script>
<style>
   .comparativa td {
       font-size: 0.75rem !important;
   }
   .comparativa a {
        color: #6565b9;
        font-size: 0.9rem;
        line-height: 0.75rem;
        font-weight: bold;
    }
 
      .div-filtro{
        float: left;
        margin: 5px;
      }
      .filters{
        position: relative;
        margin-bottom: 1.5rem !important;
      }
      .crear-nuevo {
        font-weight: bold;
        background: #dfeaff;
    }
</style>
@endsection

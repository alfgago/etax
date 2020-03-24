@extends('layouts/app')

@section('title') 
    Comparativo de facturas emitidas - QuickBooks
@endsection

@section('content') 
<div class="row">
  <div class="col-xl-12 col-lg-12 col-md-12">
        
      

        <div class="form-row">
            
            <div class="form-group col-md-12 periodo-actual filters">
                  <div class="div-filtro">
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
                    <label>Refrescar datos</label>
                    <div>
                    <a type="button" class="btn btn-success inline-button" href="#" onclick="resyncQuickbooks();">
                        <i class="fa fa-refresh" aria-hidden="true"></i> <span class="">Re-sincronizar datos de Quickbooks</span>
                    </a>
                    </div>
                  </div>
            </div>
          
            <div class="form-group col-md-6">  
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                        Facturas Quickbooks
                        </h3>
                    </div>
                    
                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered comparativa" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                                <tr>
                                    <th># Documento</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $fac)
                                <tr class="item-tabla item-index-{{ $loop->index }}">
                                    <td>{!! @$fac->numero_qb ?? "No existe en Quickbooks. <a href='#'>COPIAR</a>" !!}</td>
                                    <td>{{ @$fac->cliente_qb ?? '-' }}</td>
                                    <td>{{ @$fac->fecha_qb ?? '-' }}</td>
                                    <td>{{ @$fac->total_qb ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="form-group col-md-6">  
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                        Facturas eTax
                        </h3>
                    </div>
                    
                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered comparativa" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                                <tr>
                                    <th># Documento</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $fac)
                                <tr class="item-tabla item-index-{{ $loop->index }}">
                                    <td>
                                        {!! @$fac->numero_etax ?? "No existe en eTax. <a href='#' onclick='sendQbaetax( $fac->qb_id );'>COPIAR</a>" !!}
                                        <form id="qbaetax-form-{{ @$fac->qb_id }}" method="POST" class="inline-form" action="/quickbooks/emitida-qbaetax">
                                            @csrf
                                            <input type="hidden" name="invoiceId" value="{{ @$fac->qb_id }}">
                                        </form>
                                    </td>
                                    <td>{{ @$fac->cliente_etax ?? '-' }}</td>
                                    <td>{{ @$fac->fecha_etax ?? '-' }}</td>
                                    <td>{{ @$fac->total_etax ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
         
        <button id="btn-submit" type="submit" class="hidden">Guardar variables</button>
        
  </div>  
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar</button>
@endsection 

@section('footer-scripts')

<script>
    function reloadComparativo() {
        var mes = $("#input-mes").val();
        var ano = $("#input-ano").val();
        window.location.replace("/quickbooks/comparativo-emitidas/"+ano+"/"+mes);
    }
    function resyncQuickbooks() {
        var mes = $("#input-mes").val();
        var ano = $("#input-ano").val();
        window.location.replace("/quickbooks/resync-invoices/"+ano+"/"+mes);
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
        margin-bottom: 3.5rem !important;
      }
</style>
@endsection

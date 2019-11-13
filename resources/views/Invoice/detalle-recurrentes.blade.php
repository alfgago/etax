@extends('layouts/app')

@section('title') 
  	Facturas recurrentes
@endsection
@section('breadcrumb-buttons')
  <button type="submit" onclick="$('#btn-submit-form').click();"  class="btn btn-primary">Guardar factura</button>
@endsection 
@section('content') 
<div class="row">
  <div class="col-md-6">
          
      <label><b>Cliente: </b></label>{{$recurringInvoice->invoice->clientName()}}<br/>
      <label><b>Moneda: </b></label>
                    {{$recurringInvoice->invoice->currency}}
                    @if($recurringInvoice->invoice->currency != 'CRC') 
                       {{number_format( $recurringInvoice->invoice->currency_rate, 2 )}}
                    @endif
                    <br/>
      <label><b>Total: </b></label>{{ number_format( $recurringInvoice->invoice->total, 2 ) }}<br/>
      <label><b>Proximo envio: </b></label>{{date('d/m/Y', strtotime($recurringInvoice->next_send))}}<br/>
  </div>
  <div class="col-md-6">
    <div class="col-md-6">
      <label><b>Tipo de recurrencia: </b></label>
      <div class='input-group date inputs-fecha'>
        <select class="form-control" id="recurrencia" name="recurrencia">
          <option value="0" @if($recurringInvoice->frecuency == "0" ) selected @endif >Ninguna</option>
          <option value="1" @if($recurringInvoice->frecuency == "1" ) selected @endif >Semanal</option>
          <option value="2" @if($recurringInvoice->frecuency == "2" ) selected @endif >Quincenal</option>
          <option value="3" @if($recurringInvoice->frecuency == "3" ) selected @endif >Mensual</option>
          <option value="4" @if($recurringInvoice->frecuency == "4" ) selected @endif >Anual</option>
          <option value="5" @if($recurringInvoice->frecuency == "5" ) selected @endif >Cantidad de días</option>
        </select>
      </div>
    </div>
    <div class="col-md-6 div-semanal div-recurrencia @if($recurringInvoice->frecuency != '1') hidden @endif">
      <div class="row">
        <div class="form-group col-md-12">
          <label><b>Día de la semana</b></label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="dia" name="dia">
              <option value="1" @if($recurringInvoice->valores()[0] == "1" ) selected @endif >Lunes</option>
              <option value="2" @if($recurringInvoice->valores()[0] == "2" ) selected @endif >Martes</option>
              <option value="3" @if($recurringInvoice->valores()[0] == "3" ) selected @endif >Miercoles</option>
              <option value="4" @if($recurringInvoice->valores()[0] == "4" ) selected @endif >Jueves</option>
              <option value="5" @if($recurringInvoice->valores()[0] == "5" ) selected @endif >Viernes</option>
              <option value="6" @if($recurringInvoice->valores()[0] == "6" ) selected @endif >Sabado</option>
              <option value="0" @if($recurringInvoice->valores()[0] == "0" ) selected @endif >Domingo</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 div-quincenal div-recurrencia  @if($recurringInvoice->frecuency != '2') hidden @endif">
      <div class="row">
        <div class="form-group col-md-6">
          <label><b>Primer quincena</b></label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="primer_quincena" name="primer_quincena">
              <option value="01" @if($recurringInvoice->valores()[0] == "01" ) selected @endif>01</option>
              <option value="02" @if($recurringInvoice->valores()[0] == "02" ) selected @endif>02</option>
              <option value="03" @if($recurringInvoice->valores()[0] == "03" ) selected @endif>03</option>
              <option value="04" @if($recurringInvoice->valores()[0] == "04" ) selected @endif>04</option>
              <option value="05" @if($recurringInvoice->valores()[0] == "05" ) selected @endif>05</option>
              <option value="06" @if($recurringInvoice->valores()[0] == "06" ) selected @endif>06</option>
              <option value="07" @if($recurringInvoice->valores()[0] == "07" ) selected @endif>07</option>
              <option value="08" @if($recurringInvoice->valores()[0] == "08" ) selected @endif>08</option>
              <option value="09" @if($recurringInvoice->valores()[0] == "09" ) selected @endif>09</option>
              <option value="10" @if($recurringInvoice->valores()[0] == "10" ) selected @endif>10</option>
              <option value="11" @if($recurringInvoice->valores()[0] == "11" ) selected @endif>11</option>
              <option value="12" @if($recurringInvoice->valores()[0] == "12" ) selected @endif>12</option>
              <option value="13" @if($recurringInvoice->valores()[0] == "13" ) selected @endif>13</option>
              <option value="14" @if($recurringInvoice->valores()[0] == "14" ) selected @endif>14</option>
              <option value="15" @if($recurringInvoice->valores()[0] == "15" ) selected @endif>15</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-6">
          <label for="due_date">Segunda quincena</label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="segunda_quincena" name="segunda_quincena">
              <option value="15" @if($recurringInvoice->valores()[1] == "15" ) selected @endif>15</option>
              <option value="16" @if($recurringInvoice->valores()[1] == "16" ) selected @endif>16</option>
              <option value="17" @if($recurringInvoice->valores()[1] == "17" ) selected @endif>17</option>
              <option value="18" @if($recurringInvoice->valores()[1] == "18" ) selected @endif>18</option>
              <option value="19" @if($recurringInvoice->valores()[1] == "19" ) selected @endif>19</option>
              <option value="20" @if($recurringInvoice->valores()[1] == "20" ) selected @endif>20</option>
              <option value="21" @if($recurringInvoice->valores()[1] == "21" ) selected @endif>21</option>
              <option value="22" @if($recurringInvoice->valores()[1] == "22" ) selected @endif>22</option>
              <option value="23" @if($recurringInvoice->valores()[1] == "23" ) selected @endif>23</option>
              <option value="24" @if($recurringInvoice->valores()[1] == "24" ) selected @endif>24</option>
              <option value="25" @if($recurringInvoice->valores()[1] == "25" ) selected @endif>25</option>
              <option value="26" @if($recurringInvoice->valores()[1] == "26" ) selected @endif>26</option>
              <option value="27" @if($recurringInvoice->valores()[1] == "27" ) selected @endif>27</option>
              <option value="28" @if($recurringInvoice->valores()[1] == "28" ) selected @endif>28</option>
              <option value="29" @if($recurringInvoice->valores()[1] == "29" ) selected @endif>29</option>
              <option value="30" @if($recurringInvoice->valores()[1] == "30" ) selected @endif>30</option>
              <option value="31" @if($recurringInvoice->valores()[1] == "31" ) selected @endif>31</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 div-mensual div-recurrencia  @if($recurringInvoice->frecuency != '3') hidden @endif">
      <div class="row">
        <div class="form-group col-md-12">
          <label><b>Día</b></label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="mensual" name="mensual">
              <option value="01" @if($recurringInvoice->valores()[0] == "01" ) selected @endif>01</option>
              <option value="02" @if($recurringInvoice->valores()[0] == "02" ) selected @endif>02</option>
              <option value="03" @if($recurringInvoice->valores()[0] == "03" ) selected @endif>03</option>
              <option value="04" @if($recurringInvoice->valores()[0] == "04" ) selected @endif>04</option>
              <option value="05" @if($recurringInvoice->valores()[0] == "05" ) selected @endif>05</option>
              <option value="06" @if($recurringInvoice->valores()[0] == "06" ) selected @endif>06</option>
              <option value="07" @if($recurringInvoice->valores()[0] == "07" ) selected @endif>07</option>
              <option value="08" @if($recurringInvoice->valores()[0] == "08" ) selected @endif>08</option>
              <option value="09" @if($recurringInvoice->valores()[0] == "09" ) selected @endif>09</option>
              <option value="10" @if($recurringInvoice->valores()[0] == "10" ) selected @endif>10</option>
              <option value="11" @if($recurringInvoice->valores()[0] == "11" ) selected @endif>11</option>
              <option value="12" @if($recurringInvoice->valores()[0] == "12" ) selected @endif>12</option>
              <option value="13" @if($recurringInvoice->valores()[0] == "13" ) selected @endif>13</option>
              <option value="14" @if($recurringInvoice->valores()[0] == "14" ) selected @endif>14</option>
              <option value="15" @if($recurringInvoice->valores()[0] == "15" ) selected @endif>15</option>
              <option value="16" @if($recurringInvoice->valores()[0] == "16" ) selected @endif>16</option>
              <option value="17" @if($recurringInvoice->valores()[0] == "17" ) selected @endif>17</option>
              <option value="18" @if($recurringInvoice->valores()[0] == "18" ) selected @endif>18</option>
              <option value="19" @if($recurringInvoice->valores()[0] == "19" ) selected @endif>19</option>
              <option value="20" @if($recurringInvoice->valores()[0] == "20" ) selected @endif>20</option>
              <option value="21" @if($recurringInvoice->valores()[0] == "21" ) selected @endif>21</option>
              <option value="22" @if($recurringInvoice->valores()[0] == "22" ) selected @endif>22</option>
              <option value="23" @if($recurringInvoice->valores()[0] == "23" ) selected @endif>23</option>
              <option value="24" @if($recurringInvoice->valores()[0] == "24" ) selected @endif>24</option>
              <option value="25" @if($recurringInvoice->valores()[0] == "25" ) selected @endif>25</option>
              <option value="26" @if($recurringInvoice->valores()[0] == "26" ) selected @endif>26</option>
              <option value="27" @if($recurringInvoice->valores()[0] == "27" ) selected @endif>27</option>
              <option value="28" @if($recurringInvoice->valores()[0] == "28" ) selected @endif>28</option>
              <option value="29" @if($recurringInvoice->valores()[0] == "29" ) selected @endif>29</option>
              <option value="30" @if($recurringInvoice->valores()[0] == "30" ) selected @endif>30</option>
              <option value="31" @if($recurringInvoice->valores()[0] == "31" ) selected @endif>31</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 div-anual div-recurrencia  @if($recurringInvoice->frecuency != '4') hidden @endif">
      <div class="row">
        <div class="form-group col-md-4">
          <label><b>Día</b></label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="dia_recurrencia" name="dia_recurrencia">
              <option value="01" @if($recurringInvoice->valores()[0] == "01" ) selected @endif>01</option>
              <option value="02" @if($recurringInvoice->valores()[0] == "02" ) selected @endif>02</option>
              <option value="03" @if($recurringInvoice->valores()[0] == "03" ) selected @endif>03</option>
              <option value="04" @if($recurringInvoice->valores()[0] == "04" ) selected @endif>04</option>
              <option value="05" @if($recurringInvoice->valores()[0] == "05" ) selected @endif>05</option>
              <option value="06" @if($recurringInvoice->valores()[0] == "06" ) selected @endif>06</option>
              <option value="07" @if($recurringInvoice->valores()[0] == "07" ) selected @endif>07</option>
              <option value="08" @if($recurringInvoice->valores()[0] == "08" ) selected @endif>08</option>
              <option value="09" @if($recurringInvoice->valores()[0] == "09" ) selected @endif>09</option>
              <option value="10" @if($recurringInvoice->valores()[0] == "10" ) selected @endif>10</option>
              <option value="11" @if($recurringInvoice->valores()[0] == "11" ) selected @endif>11</option>
              <option value="12" @if($recurringInvoice->valores()[0] == "12" ) selected @endif>12</option>
              <option value="13" @if($recurringInvoice->valores()[0] == "13" ) selected @endif>13</option>
              <option value="14" @if($recurringInvoice->valores()[0] == "14" ) selected @endif>14</option>
              <option value="15" @if($recurringInvoice->valores()[0] == "15" ) selected @endif>15</option>
              <option value="16" @if($recurringInvoice->valores()[0] == "16" ) selected @endif>16</option>
              <option value="17" @if($recurringInvoice->valores()[0] == "17" ) selected @endif>17</option>
              <option value="18" @if($recurringInvoice->valores()[0] == "18" ) selected @endif>18</option>
              <option value="19" @if($recurringInvoice->valores()[0] == "19" ) selected @endif>19</option>
              <option value="20" @if($recurringInvoice->valores()[0] == "20" ) selected @endif>20</option>
              <option value="21" @if($recurringInvoice->valores()[0] == "21" ) selected @endif>21</option>
              <option value="22" @if($recurringInvoice->valores()[0] == "22" ) selected @endif>22</option>
              <option value="23" @if($recurringInvoice->valores()[0] == "23" ) selected @endif>23</option>
              <option value="24" @if($recurringInvoice->valores()[0] == "24" ) selected @endif>24</option>
              <option value="25" @if($recurringInvoice->valores()[0] == "25" ) selected @endif>25</option>
              <option value="26" @if($recurringInvoice->valores()[0] == "26" ) selected @endif>26</option>
              <option value="27" @if($recurringInvoice->valores()[0] == "27" ) selected @endif>27</option>
              <option value="28" @if($recurringInvoice->valores()[0] == "28" ) selected @endif>28</option>
              <option value="29" @if($recurringInvoice->valores()[0] == "29" ) selected @endif>29</option>
              <option value="30" @if($recurringInvoice->valores()[0] == "30" ) selected @endif>30</option>
              <option value="31" @if($recurringInvoice->valores()[0] == "31" ) selected @endif>31</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-8">
          <label><b>Mes</b></label>
          <div class='input-group date inputs-fecha'>
            <select class="form-control" id="mes_recurrencia" name="mes_recurrencia">
              <option value="01" @if($recurringInvoice->valores()[1] == "01" ) selected @endif>Enero</option>
              <option value="02" @if($recurringInvoice->valores()[1] == "02" ) selected @endif>Febrero</option>
              <option value="03" @if($recurringInvoice->valores()[1] == "03" ) selected @endif>Marzo</option>
              <option value="04" @if($recurringInvoice->valores()[1] == "04" ) selected @endif>Abril</option>
              <option value="05" @if($recurringInvoice->valores()[1] == "05" ) selected @endif>Mayo</option>
              <option value="06" @if($recurringInvoice->valores()[1] == "06" ) selected @endif>Junio</option>
              <option value="07" @if($recurringInvoice->valores()[1] == "07" ) selected @endif>Julio</option>
              <option value="08" @if($recurringInvoice->valores()[1] == "08" ) selected @endif>Agosto</option>
              <option value="09" @if($recurringInvoice->valores()[1] == "09" ) selected @endif>Setiembre</option>
              <option value="10" @if($recurringInvoice->valores()[1] == "10" ) selected @endif>Octubre</option>
              <option value="11" @if($recurringInvoice->valores()[1] == "11" ) selected @endif>Noviembre</option>
              <option value="12" @if($recurringInvoice->valores()[1] == "12" ) selected @endif>Diciembre</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6 div-cantidad-dias div-recurrencia  @if($recurringInvoice->frecuency != '5') hidden @endif">
      <div class="row">
        <div class="form-group col-md-12">
          <label><b>Cantidad de días</b></label>
          <div class='input-group date inputs-fecha'>
            <input type="number" min="0" class="form-control" value="{{$recurringInvoice->valores()[0]}}" id="cantidad_dias" name="cantidad_dias"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>  
<div class="row">
  <div class="col-md-12">

      	<table id="invoice-table" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th data-priority="2">Comprobante</th>
              <th data-priority="3">Receptor</th>
              <th data-priority="4">Tipo Doc.</th>
              <th data-priority="5">Moneda</th>
              <th data-priority="5">Subtotal</th>
              <th data-priority="5">Monto IVA</th>
              <th data-priority="4">Total</th>
              <th data-priority="4">F. Generada</th>
              <th data-priority="1">Estado</th>
            </tr>
          </thead>
          <tbody>
            @if ( $recurringInvoice->enviadas()->count() )
              @foreach ( $recurringInvoice->enviadas() as $data )
                <tr>
                  <td>{{$data->document_number}}</td>
                  <td>
                      @if (!empty($data->client_first_name) )
                        {{$data->client_first_name.' '.$data->client_last_name}}
                      @endif
                      @if(empty($data->client_first_name) )
                        {{$data->clientName()}}
                      @endif
                  </td>
                  <td>{{$data->documentTypeName()}}</td>
                  <td>
                    {{$data->currency}}
                    @if($data->currency != 'CRC') 
                       {{number_format( $data->currency_rate, 2 )}}
                    @endif
                  </td>
                  <td>{{number_format( $data->subtotal, 2 )}}</td>
                  <td>{{number_format( $data->iva_amount, 2 )}}</td>
                  <td>{{number_format( $data->total, 2 )}}</td>
                  <td>{{$data->generatedDate()->format('d/m/Y')}}</td>
                  <td>
                    @if ($data->hacienda_status == '03' || $data->hacienda_status == '30') 
                      <div class="green">  <span class="tooltiptext">Aceptada</span></div>
                    @endif
                    @if ($data->hacienda_status == '04') 
                      <div class="red"> <span class="tooltiptext">Rechazada</span></div>
                    @endif
                    @if ($data->hacienda_status == '05') 
                      <div class="orange"> <span class="tooltiptext">Esperando respuesta de hacienda</span></div>
                    @endif
                    @if ( $data->hacienda_status == '01') 
                      <div class="yellow"><span class="tooltiptext">Procesando...</span></div>
                    @endif
                    @if ($data->hacienda_status == '99' ) 
                      <div class="blue"><span class="tooltiptext">Programada...</span></div>
                    @endif
                  </td>
                </tr>
              @endforeach
            @endif

          </tbody>
        </table>
        
  </div>  
</div>
@endsection

@section('footer-scripts')


<script>
$(document).ready(function(){

  $('#recurrencia').change(function() {
    var recurrencia = $(this).val();
    $(".div-recurrencia").addClass("hidden");
    if(recurrencia == 1){
      $(".div-semanal").removeClass("hidden");
    }
    if(recurrencia == 2){
      $(".div-quincenal").removeClass("hidden");
    }
    if(recurrencia == 3){
      $(".div-mensual").removeClass("hidden");
    }
    if(recurrencia == 4){
      $(".div-anual").removeClass("hidden");
    }
    if(recurrencia == 5){
      $(".div-cantidad-dias").removeClass("hidden");
    }          
  });
});


</script>

@endsection
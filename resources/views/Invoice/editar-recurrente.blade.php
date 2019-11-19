@extends('layouts/app')

@section('title')
    Factura recurrente
@endsection

@section('content')
    <div class="row form-container">
        <div class="col-md-12">
      <form method="POST" action="/facturas-emitidas/send">

          @csrf
          
          <input type="hidden" id="current-index" value="0">
          <input type="hidden" class="form-control" id="default_product_category" value="{{$company->default_product_category}}">
          <input type="hidden" class="form-control" id="default_vat_code" value="{{$company->default_vat_code}}">
                <input type="hidden" id="current-index" value="{{ count($invoice->items) }}">

                <div class="form-row">
                    <div class="col-md">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <h3>
                                            Cliente
                                        </h3>
                                    </div>

                                    <div class="form-group col-md-12 with-button">
                                        <label for="cliente">Seleccione el cliente</label>
                                        <select class="form-control select-search" name="client_id" id="client_id" placeholder="" onlyread required >
                                            <option value=''>-- Seleccione un cliente --</option>
                                            @foreach ( currentCompanyModel()->clients as $cliente )
                                                <option  {{ $invoice->client_id == $cliente->id ? 'selected' : '' }} value="{{ $cliente->id }}" >{{ $cliente->toString() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <h3>
                                            Moneda
                                        </h3>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="currency">Divisa</label>
                                        <select class="form-control" name="currency" id="moneda" required >
                                            <option value="CRC" {{ $invoice->currency == 'CRC' ? 'selected' : '' }}>CRC</option>
                                            <option value="USD" {{ $invoice->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-8">
                                        <label for="currency_rate">Tipo de cambio</label>
                                        <input type="text"  class="form-control"  data-rates="{{$rate}}" name="currency_rate" id="tipo_cambio" value="{{ $invoice->currency_rate }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <h3>
                                    Detalle
                                </h3>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="subtotal">Subtotal </label>
                                <input type="text" class="form-control" name="subtotal" id="subtotal" placeholder="" onlyread="true" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="iva_amount">Monto IVA </label>
                                <input type="text" class="form-control" name="iva_amount" id="monto_iva" placeholder="" onlyread="true" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="total">Total</label>
                                <input type="text" class="form-control total" name="total" id="total" placeholder="" onlyread="true" required>
                            </div>

                            <div class="form-group col-md-12">
                            <div onclick="abrirPopup('linea-popup');" class="btn btn-dark btn-agregar">Agregar línea</div>
                            <div onclick="abrirPopup('otros-popup');" class="btn btn-dark btn-agregar btn-otroscargos">Agregar otros cargos</div>
                          </div>

                        </div>

                    </div>


                    <div class="col-md offset-md-1">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <h3>
                                    Datos generales
                                </h3>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="document_number">Número de documento</label>
                                <input type="text" class="form-control"  name="document_number" id="document_number" value="{{ $document_number }}" onlyread required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="document_key">Clave de factura</label>
                                <input type="text" class="form-control" onlyread name="document_key" id="document_key" value="{{ $document_key }}" >
                            </div>

                            <div class="form-group col-md-6">
                                <label for="generated_date">Fecha</label>
                                <div class='input-group date inputs-fecha'>
                                    <input id="fecha_generada"  class="form-control input-fecha" placeholder="dd/mm/yyyy" name="generated_date" required value="{{date('d/m/Y', strtotime($recurringInvoice->next_send))}}">
                                    <span class="input-group-addon">
                          <i class="icon-regular i-Calendar-4"></i>
                        </span>
                                </div>
                            </div>

                            <div class="form-group col-md-4 hidden">
                                <label for="hora">Hora</label>
                                <div class='input-group date inputs-hora'>
                                    <input id="hora"  class="form-control input-hora" name="hora" required value="{{ $invoice->generatedDate()->format('g:i A') }}">
                                    <span class="input-group-addon">
                          <i class="icon-regular i-Clock"></i>
                        </span>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="due_date">Fecha de vencimiento</label>
                                <div class='input-group date inputs-fecha'>
                                    <input id="fecha_vencimiento"  class="form-control input-fecha" placeholder="dd/mm/yyyy" name="due_date" required value="{{date('d/m/Y', strtotime($recurringInvoice->proximoVencimiento()))}}">
                                    <span class="input-group-addon">
                        <i class="icon-regular i-Calendar-4"></i>
                      </span>
                                </div>
                            </div>
<div class="form-group col-md-6">
      <label>Tipo de recurrencia:</label>
      <div class='input-group'>
        <select class="form-control" id="recurrencia" name="recurrencia">
          <option value="0" @if($recurringInvoice->frecuency == "0" ) selected @endif >Ninguna</option>
          <option value="1" @if($recurringInvoice->frecuency == "1" ) selected @endif >Semanal</option>
          <option value="2" @if($recurringInvoice->frecuency == "2" ) selected @endif >Quincenal</option>
          <option value="3" @if($recurringInvoice->frecuency == "3" ) selected @endif >Mensual</option>
          <option value="4" @if($recurringInvoice->frecuency == "4" ) selected @endif >Anual</option>
          <option value="5" @if($recurringInvoice->frecuency == "5" ) selected @endif >Cantidad de días</option>
        </select>
        <input id="id_recurrente"  class="form-control hidden" name="id_recurrente" required value="{{$recurringInvoice->id}}">
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
          <label>Primer quincena</label>
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
          <label>Día</label>
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
          <label>Día</label>
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
                            <div class="form-group col-md-12">
                                <label for="payment_type">Actividad Comercial</label>
                                <div class="input-group">
                                    <select id="commercial_activity" name="commercial_activity" class="form-control" required>
                                        @foreach ( $arrayActividades as $actividad )
                                            <option {{ $invoice->commercial_activity == $actividad->codigo ? 'selected' : '' }} value="{{ $actividad->codigo }}" >{{ $actividad->codigo }} - {{ $actividad->actividad }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="text" value="{{$invoice->id}}" name="invoice_id" hidden>
                            <div class="form-group col-md-6">
                                <label for="sale_condition">Condición de venta</label>
                                <div class="input-group">
                                    <select id="condicion_venta" name="sale_condition" class="form-control" required>
                                        <option {{ $invoice->sale_condition == '01' ? 'selected' : '' }} value="01">Contado</option>
                                        <option {{ $invoice->sale_condition == '02' ? 'selected' : '' }} value="02">Crédito</option>
                                        <option {{ $invoice->sale_condition == '03' ? 'selected' : '' }} value="03">Consignación</option>
                                        <option {{ $invoice->sale_condition == '04' ? 'selected' : '' }} value="04">Apartado</option>
                                        <option {{ $invoice->sale_condition == '05' ? 'selected' : '' }} value="05">Arrendamiento con opción de compra</option>
                                        <option {{ $invoice->sale_condition == '06' ? 'selected' : '' }} value="06">Arrendamiento en función financiera</option>
                                        <option {{ $invoice->sale_condition == '99' ? 'selected' : '' }} value="99">Otros</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="payment_type">Método de pago</label>
                                <div class="input-group">
                                    <select id="medio_pago" name="payment_type" class="form-control" onchange="toggleRetencion();" required>
                                        <option {{ $invoice->payment_type == '01' ? 'selected' : '' }} value="01" selected>Efectivo</option>
                                        <option {{ $invoice->payment_type == '02' ? 'selected' : '' }} value="02">Tarjeta</option>
                                        <option {{ $invoice->payment_type == '03' ? 'selected' : '' }} value="03">Cheque</option>
                                        <option {{ $invoice->payment_type == '04' ? 'selected' : '' }} value="04">Transferencia-Depósito Bancario</option>
                                        <option {{ $invoice->payment_type == '05' ? 'selected' : '' }} value="05">Recaudado por terceros</option>
                                        <option {{ $invoice->payment_type == '99' ? 'selected' : '' }} value="99">Otros</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-12" id="field-retencion" style="display:none;">
                                <label for="retention_percent">Porcentaje de retención</label>
                                <div class="input-group">
                                    <select id="retention_percent"  name="retention_percent" class="form-control" required>
                                        <option value="6" {{ $invoice->retention_percent == 6 ? 'selected' : '' }}>6%</option>
                                        <option value="3" {{ $invoice->retention_percent == 3 ? 'selected' : '' }}>3%</option>
                                        <option value="0" {{ $invoice->retention_percent == 0 ? 'selected' : '' }}>Sin retención</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="other_reference">Referencia</label>
                                <input type="text"  class="form-control" name="other_reference" id="referencia" value="{{ $invoice->other_reference }}" >
                            </div>

                            <div class="form-group col-md-6">
                                <label for="buy_order">Orden de compra</label>
                                <input type="text"  class="form-control" name="buy_order" id="orden_compra" value="{{ $invoice->buy_order }}" >
                            </div>

                            <div class="form-group col-md-12">
                                <label for="description">Notas</label>
                                <input type="text" class="form-control" name="description" id="notas" placeholder="" value="{{ $invoice->description }}">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="form-row" id="tabla-items-factura" >

                    <div class="form-group col-md-12">
                        <h3>
                            Líneas de factura
                        </h3>
                    </div>

                    <div class="form-group col-md-12">
                        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%" >
                            <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Cant.</th>
                                <th>Unidad</th>
                                <th>Precio unitario</th>
                                <th>Tipo/Categoría IVA</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ( $invoice->items as $item )
                                <tr class="item-tabla item-index-{{ $loop->index }}" index="{{ $loop->index }}" attr-num="{{ $loop->index }}" id="item-tabla-{{ $loop->index }}">
                                    <td><span class="numero-fila">{{ $loop->index+1 }}</span>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->item_count }}</td>
                                    <td>{{ \App\Variables::getUnidadMedicionName($item->measure_unit) }}</td>
                                    <td>{{ $item->unit_price }} </td>
                                    <td>{{ \App\Variables::getTipoRepercutidoIVAName($item->iva_type) }} <br> - {{ @\App\ProductCategory::find($item->product_type)->name }} </td>
                                    <td>{{ $item->subtotal }}</td>
                                    <td>{{ $item->iva_amount }}</td>
                                    <td>{{ $item->total }}</td>
                                    <td class='acciones'>
                                        <span title='Editar linea' class='btn-editar-item text-success mr-2' onclick="abrirPopup('linea-popup'); cargarFormItem({{ $loop->index }});"> <i class="fa fa-pencil" aria-hidden="true"></i> </span>
                                        <span title='Eliminar linea' class='btn-eliminar-item text-danger mr-2' onclick='eliminarItem({{ $loop->index }});' > <i class="fa fa-trash-o" aria-hidden="true"></i> </span>
                                    </td>
                                    <td class="hidden">
                                        <input type="hidden" class='numero' name="items[{{ $loop->index }}][item_number]" value="{{ $loop->index+1 }}">
                                        <input type="hidden" class="item_id" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}"> </td>
                                    <input type="hidden" class='codigo' name="items[{{ $loop->index }}][code]" value="{{ $item->code }}">
                                    <input type="hidden" class='nombre' name="items[{{ $loop->index }}][name]" value="{{ $item->name }}">
                                    <input type="hidden" class='tipo_producto' name="items[{{ $loop->index }}][product_type]" value="{{ $item->product_type }}">
                                    <input type="hidden" class='cantidad' name="items[{{ $loop->index }}][item_count]" value="{{ $item->item_count }}">
                                    <input type="hidden" class='unidad_medicion' name="items[{{ $loop->index }}][measure_unit]" value="{{ $item->measure_unit }}">
                                    <input type="hidden" class='precio_unitario' name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}">
                                    <input type="hidden" class='tipo_iva' name="items[{{ $loop->index }}][iva_type]" value="{{ $item->iva_type }}">
                                    <input type='hidden' class='porc_identificacion_plena' value='0'>
                                    <input type='hidden' class='discount_type' name='items[{{ $loop->index }}][discount_type]' value='{{ $item->discount_type }}'>
                                    <input type='hidden' class='discount' name='items[{{ $loop->index }}][discount]' value='{{ $item->discount }}'>
                                    <input class="subtotal" type="hidden" name="items[{{ $loop->index }}][subtotal]" value="{{ $item->subtotal }}">
                                    <input class="porc_iva" type="hidden" name="items[{{ $loop->index }}][iva_percentage]" value="{{ $item->iva_percentage }}">
                                    <input class="monto_iva" type="hidden" name="items[{{ $loop->index }}][iva_amount]" value="{{ $item->iva_amount }}">
                                    <input class="total" type="hidden" name="items[{{ $loop->index }}][total]" value="{{ $item->total }}">
                                    <input class="is_identificacion_especifica" type="hidden" name="items[{{ $loop->index }}][is_identificacion_especifica]" value="{{ $item->is_identificacion_especifica }}">
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @include( 'Invoice.form-linea' )
                <div class="btn-holder hidden">

                    <input type="text" hidden value="{{ $document_type }}" name="document_type" id="document_type">
                    <button id="btn-submit" type="submit" class="btn btn-primary">Enviar factura electrónica</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('breadcrumb-buttons')
    <button id='btn-submit-fe' onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar factura</button>
@endsection
@section('footer-scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

    <script>
        $(document).ready(function(){
            @if( @$document_type != '08' )
              if( $('#default_vat_code').length ){
                $('#tipo_iva').val( $('#default_vat_code').val() ).change();
              }else{
                $('#tipo_iva').val( 'B103' ).change();
              }
            @else
              $('#tipo_iva').val( 'B003' ).change();
            @endif

            @if (@$document_type == '09')
              $('#tipo_iva').val( 'B150' ).change();
            @endif

            $('#moneda').change(function() {
              if ($(this).val() == 'USD') {
                $('#tipo_cambio').val($('#tipo_cambio').data('rates'))
              } else {
                $('#tipo_cambio').val('1.00')
              }
            });

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
            $('#tipo_producto').val(17).change();

            var subtotal = 0;
            var monto_iva = 0;
            var total = 0;
            $('.item-tabla').each(function(){
                var s = parseFloat($(this).find('.subtotal').val());
                var m = parseFloat($(this).find('.monto_iva').val());
                var t = parseFloat($(this).find('.total').val());
                subtotal += s;
                monto_iva += m;
                total += t;
            });

            $('#subtotal').val( fixComas(subtotal) );
            $('#monto_iva').val( fixComas(monto_iva) );
            $('#total').val( fixComas(total) );
 
            toggleRetencion();
        });

        function toggleRetencion() {
            var metodo = $("#medio_pago").val();
            if( metodo == '02' ){
                $("#field-retencion").show();
            }else {
                $("#field-retencion").hide();
            }
        }
    </script>

@endsection
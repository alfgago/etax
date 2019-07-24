@extends('layouts/app')

@section('title')
    Seleccion de empresas
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form method="POST" action="/payment/seleccion-empresas">
                @csrf
                @method('post') 

               <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>Seleccionar empresas activadas
                        </h3>
                    </div>
                </div>
                @php
                    $i = 0;
                @endphp
                @foreach ($companies as $company)
                    @php
                        $i = $i + 1;
                    @endphp
                   <div class="form-row">
                        <div class="form-group col-md-12">
                            <input type='checkbox'  id="empresa-{{@$i}}" name='empresas[]' class="checked_company" value="{{@$company->id}}" @if ($company->status === 1 ) checked @else disabled="true" @endif / >
                            <label for="first_name">{{@$company->business_name}}</label>
                        </div>
                    </div>
                @endforeach 
                <button id="btn-submit" type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

            <input type="number" value="{{@$companies_puedo}}" hidden id="cantidad_compania_posibles">

            <input type="number" value="{{@$i}}" hidden id="cantidad_compania_disponibles">
@endsection

@section('footer-scripts')
    <script>
        $(".checked_company").change(function(){
            var companies_disponibles = $("#cantidad_compania_disponibles").val();
            var companies_posibles = $("#cantidad_compania_posibles").val();
            var companies_seleccionadas = 0;
            for(var i = 1 ; i <= companies_disponibles; i++){
                if($("#empresa-"+i).prop("checked")){
                    companies_seleccionadas++;
                }
            }
            if(companies_posibles <= companies_seleccionadas){
                 $(".checked_company:not(:checked)").prop( "disabled", true );
            }else{
                $(".checked_company").removeAttr("disabled");
            }
        });
    </script>
@endsection

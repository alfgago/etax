@extends('layouts/app')

@section('title')
    Seleccion de empresas
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <input type="number" value="{{@$companies_puedo}}" id="cantidad_compania">
            <form method="POST" action="/payment/seleccion-empresas">
                @csrf
                @method('patch') 

               <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>Seleccionar empresas activadas
                        </h3>
                    </div>
                </div>
                @foreach ($companies as $company)
                   <div class="form-row">
                        <div class="form-group col-md-12">
                            <input type='checkbox' name='empresas[]' class="checked_company" value="{{@$company->id}}" @if ($company->status === 1 ) checked @endif / >
                            <label for="first_name">{{@$company->business_name}}</label>
                        </div>
                    </div>
                @endforeach
                <button id="btn-submit" type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

@endsection

@section('footer-scripts')
    <script>
        $(".checked_company").change(function(){
            alert("hola");
        });
    </script>
@endsection

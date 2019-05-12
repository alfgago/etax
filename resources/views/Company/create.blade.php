@extends('layouts/app')

@section('title')
    Crear empresas
@endsection

@section('content')

<div class="row">
    <div class="col-xl-9 col-lg-12 col-md-12">

        <form method="POST" action="/empresas">

            <div class="form-row">
                <div class="form-group col-md-12">
                    <h3>
                        Información de empresas
                    </h3>
                </div>

                @csrf
                @include( 'Company.form' )

            </div>

            <button id="btn-submit" type="submit" class="hidden btn btn-primary">Confirmar empresas</button>            

        </form>

    </div>
</div>
@endsection

@section('breadcrumb-buttons')
  <button onclick="$('#btn-submit').click();" class="btn btn-primary">Guardar empresa</button>
@endsection 

@section('footer-scripts')


@endsection

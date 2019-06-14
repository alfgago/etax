@extends('layouts/app')

@section('title')
    Comprar Productos eTax
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-12 col-md-12">

            <form method="POST" action="/sale/payment-create">

                @csrf
                @method('patch')

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <h3>
                            Compra de productos
                        </h3>
                    </div>
                    @include( 'Sales.newSaleForm' )

                </div>

                <button id="btn-submit" type="submit" class="hidden">Comprar</button>

            </form>

        </div>
    </div>
@endsection

@section('breadcrumb-buttons')
    <button onclick="$('#btn-submit').click();" class="btn btn-primary">Comprar</button>
@endsection

@section('footer-scripts')

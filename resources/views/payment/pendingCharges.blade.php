@extends('layouts/app')

@section('title')
    Pagos pendientes
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="tabbable verticalForm">
                <div class="row">
                    <div class="col-3">
                        <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @if( !auth()->user()->is_guest )
                                <li>
                                    <a class="nav-link" aria-selected="false" href="/payments-methods">M&eacute;todos de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link" aria-selected="false" href="/payments">Historial de pagos</a>
                                </li>
                                <li>
                                    <a class="nav-link active" aria-selected="true" href="/usuario/perfil">Cargos Pendientes</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-9">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" role="tabpanel">
                                <h3 class="card-title">Pagos Pendientes</h3>
                                <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Descripcion</th>
                                        <th>Monto</th>
                                        <th>Moneda</th>
                                        <th>Fecha</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ( $charges['chargesCount'] > 0 )
                                        @foreach($charges['userCharges'] as $charge)
                                            @if($charge)
                                                <tr>
                                                    <td>{{$charge['chargeDescription']}}</td>
                                                    <td>{{$charge['transactionAmount']}}</td>
                                                    <td>{{$charge['transactionCurrency']}}</td>
                                                    <td>{{$charge['chargeDateTime']}}</td>
                                                    {{--<td>
                                                        @if( auth()->user()->isOwnerOfTeam($team) )
                                                            <form id="delete-form-{{ $company_detail->id }}" class="inline-form" method="POST" action="/empresas/{{ $company_detail->id }}" >
                                                                @csrf
                                                                @method('delete')
                                                                <a type="button" class="text-danger mr-2" title="Eliminar empresa" style="display: inline-block; background: none; border: 0;" onclick="confirmDelete({{ $company_detail->id }});">
                                                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                                                </a>
                                                            </form>
                                                        @endif
                                                    </td>--}}
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

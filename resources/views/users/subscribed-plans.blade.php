@extends('layouts/app')

@section('title')
Mis Planes Suscritos
@endsection

@section('breadcrumb-buttons')
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">

        @if(session('success'))

        <div role="alert" class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('success')}}

        </div>
        @endif

        @if(session('error'))

        <div role="alert" class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{session('error')}}

        </div>
        @endif        

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/overview">Visión general</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/general">Editar información personal</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/seguridad">Seguridad</a>
                        </li>
                        <li class="active">
                            <a class="nav-link active" aria-selected="true" href="/usuario/planes">Mis Planes Suscritos</a>
                        </li>
                        <li>
                            <a class="nav-link" aria-selected="false" href="/usuario/empresas">Empresas</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0">       
                        <div class="tab-pane fade show active" id="" role="tabpanel">                            

                            <h3 class="card-title">Mis Planes Suscritos</h3>                                
                            <a class="btn btn-warning pull-right" href="{{route('plans.show-data')}}">Purchase Another Plan</a>
                            <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Plan No.</th>
                                        <th>Plan Name</th>
                                        <th>Purchased On</th>                    
                                        <th>Expiry Date</th>
                                        <th>Companies Registered</th>
                                        <th>Invited Admins</th>
                                        <th>Invited read-only users</th>
                                        <th>Status</th>
                                        <th>Action</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($plans))
                                    @foreach ( $plans as $row )
                                    @php $invited_users = get_invited_users($row['unique_no']);@endphp                                    
                                    <tr>
                                        <td>{{ $row['unique_no'] }}</td>
                                        <td>{{ ucfirst($row['plan_type']).'-'.$row['plan_name'] }}</td>
                                        <td>{{ date('d-M-Y',strtotime($row['created_at'])) }}</td>
                                        <td>{{ date('d-M-Y',strtotime($row['expiry_date'])) }}</td>
                                        <td>@if(plan_registered_companies($row['unique_no']) > 0)<a href="/usuario/empresas?plan={{encrypt($row['unique_no'])}}" title="View Registered Companies">{{ plan_registered_companies($row['unique_no']) }}</a>@else{{plan_registered_companies($row['unique_no'])}}@endif</td>
                                        <td>@if($invited_users['admin_count'] > 0)<a href="/usuario/usuarios-invitados?type=admin&plan={{encrypt($row['unique_no'])}}">{{ $invited_users['admin_count'] }}</a>@else{{$invited_users['admin_count']}}@endif</td>
                                        <td>@if($invited_users['readonly_count'] > 0)<a href="/usuario/usuarios-invitados?type=readonly&plan={{encrypt($row['unique_no'])}}">{{ $invited_users['readonly_count'] }}</a>@else{{$invited_users['readonly_count']}}@endif</td>
                                        <td>{!! get_plan_status($row['unique_no']) !!}</td>
                                        <td>
                                            @if(empty($row['cancellation_token']) || token_expired($row['cancellation_token']))                                            
                                            @if(preg_replace('/<label[^>]*?>([\\s\\S]*?)<\/label>/', '\\1', get_plan_status($row['unique_no'])) == 'Active')
                                                <form class="inline-form" method="POST" action="/plans/cancel-plan/{{ $row['unique_no'] }}" >
                                                    @csrf
                                                    @method('patch')
                                                    <button type="submit" title="Cancel Plan" class="text-danger mr-2">
                                                        <i class="nav-icon i-Close-Window font-weight-bold"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @else
                                                <label class="badge badge-warning">Cancellation Requested</label>
                                                @endif                                                                                        
                                        </td>
                                    </tr>
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

@section('footer-scripts')

@endsection
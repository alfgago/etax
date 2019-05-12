@extends('layouts/app')

@section('title')
Companies
@endsection

@section('breadcrumb-buttons')
@can('team-create')
<a type="submit" class="btn btn-primary {{$data['class']}}" href="{{$data['url']}}">Create New Company</a>
@endcan
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID-Number</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if ( $teams->count() )
                @foreach($teams as $team)
                @php $company_detail = get_company_details($team->company_id);  @endphp
                @if($company_detail)
                <tr>
                    <td>{{$team->name}}</td>
                    <td>{{$company_detail->id_number}}</td>
                    <td>{{$company_detail->email}}</td>
                    <td>{{!empty($company_detail->address) ? $company_detail->address:''}}</td>
                    <td>{{$company_detail->phone}}</td>
                    <td>
                        @if(auth()->user()->isOwnerOfTeam($team))
                        <span class="label label-success">Owner</span>
                        @else
                        <span class="label label-primary">Member</span>
                        @endif
                    </td>
                    <td>
                        @if(!auth()->user()->isOwnerOfTeam($team))
                        @if(get_plan_invitation($team->company_id,auth()->user()->id) && get_plan_invitation($team->company_id,auth()->user()->id)->is_read_only == '1')
                        <a href="{{route('Company.edit', 'id='.$team->company_id)}}" class="text-primary mr-2" title="View Company Members & Profile">
                            <i class="nav-icon i-Eye font-weight-bold"></i>
                        </a>
                        @endif
                        @endif
                        @if(auth()->user()->isOwnerOfTeam($team) || (get_plan_invitation($team->company_id,auth()->user()->id) && get_plan_invitation($team->company_id,auth()->user()->id)->is_admin == '1'))
                        <a href="{{route('Company.edit','id='.$team->company_id)}}" title="Edit Company Details" class="text-success mr-2">
                            <i class="nav-icon i-Pen-2 font-weight-bold"></i>
                        </a>
                        @endif
                        @if(auth()->user()->isOwnerOfTeam($team))
                        <form class="inline-form" method="POST" action="{{route('teams.destroy', $team)}}" style="display: inline-block;">
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-danger mr-2" title="Delete" style="display: inline-block; background: none; border: 0;">
                                <i class="nav-icon i-Close-Window font-weight-bold"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

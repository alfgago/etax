@extends('layouts/app')
@section('title')
Company Profile
@endsection

@section('breadcrumb-buttons')
<a class="btn btn-primary" href="{{route('teams.index')}}">Back</a>
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

        @if($errors->count())

        <div role="alert" class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            {{$errors->first()}}

        </div>
        @endif

        <div class="tabbable verticalForm">
            <div class="row">
                <div class="col-3">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <li class="active">
                            <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Information Basic</a>
                        </li>
                        <li>
                            <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Addtional Information</a>
                        </li>
                        <li>
                            <a class="nav-link" id="v-pills-certificate-tab" data-toggle="pill" href="#v-pills-certificate" role="tab" aria-controls="v-pills-certificate" aria-selected="false">Facturación electrónica</a>
                        </li>
                        <li>
                                <a class="nav-link" id="v-pills-team-tab" data-toggle="pill" href="#v-pills-team" role="tab" aria-controls="v-pills-team" aria-selected="false">Equipo</a>
                        </li>
                    </ul>
                </div>
                <div class="col-9">
                    <div class="tab-content p-0" id="v-pills-tabContent">                        
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <div class="form-row">
                                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} col-md-6">
                                            <label for="inputTestl4">Name</label>
                                            <?php $fname = isset($company->name) ? $company->name : null; ?>
                                            {!! Form::text('name',$fname,['placeholder' => 'Name','id'=>'inputTestl4','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('first_name'))
                                            <div class="error">{{ $errors->first('name') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }} col-md-6 ">
                                            <label for="inputText">Type</label>
                                            <?php $company_type = isset($company->type) ? $company->type : null; ?>
                                            <select class="form-control" name="type" id="type_company" required disabled>
                                                <option value=" " >Select Option</option>
                                                <option value="fisica" {{ @$company_type == 'fisica' ? 'selected' : '' }} >Física</option>
                                                <option value="juridica" {{ @$company_type == 'juridica' ? 'selected' : '' }}>Jurídica</option>
                                                <option value="dimex" {{ @$company_type == 'dimex' ? 'selected' : '' }}>DIMEX</option>
                                                <option value="extranjero" {{ @$company_type == 'extranjero' ? 'selected' : '' }}>NITE</option>
                                                <option value="nite" {{ @$company_type == 'nite' ? 'selected' : '' }}>Extranjero</option>
                                                <option value="otro" {{ @$company_type == 'otro' ? 'selected' : '' }}>Otro</option>
                                            </select>
                                            @if ($errors->has('last_name'))
                                            <div class="error">{{ $errors->first('last_name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">Identification Number</label>
                                            <?php $id_number = isset($company->id_number) ? $company->id_number : null; ?>
                                            {!! Form::text('id_number',$id_number,['placeholder' => 'Identification Number','id'=>'inputTest1','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('id_number'))
                                            <div class="error">{{ $errors->first('id_number') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" style="display:none" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <div class="form-row">
                                        <div class="form-group{{ $errors->has('invoice_email') ? ' has-error' : '' }} col-md-6">
                                            <label for="inputTestl4">Invoice Email</label>
                                            <?php $invoice_email = isset($company->invoice_email) ? $company->invoice_email : null; ?>
                                            {!! Form::text('invoice_email',$invoice_email,['placeholder' => 'Invoice Email','id'=>'inputTestl4','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('invoice_email'))
                                            <div class="error">{{ $errors->first('invoice_email') }}</div>
                                            @endif

                                        </div>
                                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }} col-md-6">
                                            <label for="inputTestl5">Phone</label>
                                            <?php $mobile = isset($company->phone) ? $company->phone : null; ?>
                                            {!! Form::number('phone',$mobile,['placeholder' => 'Phone','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('phone'))

                                            <div class="error">{{ $errors->first('phone') }}</div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Email</label>
                                            <?php $email = isset($company->email) ? $company->email : null; ?>
                                            {!! Form::text('email',$email,['placeholder' => 'email','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('email'))
                                            <div class="error">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Default currency</label>
                                            <?php $default_currency = isset($company->default_currency) ? $company->default_currency : null; ?>
                                            {!! Form::text('default_currency',$default_currency,['placeholder' => 'Default currency','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('default_currency'))
                                            <div class="error">{{ $errors->first('default_currency') }}</div>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Country</label>
                                            <select class="form-control" name="country" id="country" value="{{ @$company->country }}" required disabled>
                                                <option value="CR" selected>Costa Rica</option>
                                            </select>
                                            @if ($errors->has('country'))
                                            <div class="error">{{ $errors->first('country') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">state</label>
                                            <select class="form-control" name="state" id="state" value="{{ @$company->state }}" onchange="fillCantones();" disabled>
                                            </select>
                                            @if ($errors->has('state'))
                                            <div class="error">{{ $errors->first('state') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">City</label>
                                            <select class="form-control" name="city" id="city" value="{{ @$company->city }}" onchange="fillDistritos();" disabled>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">District</label>
                                            <select class="form-control" name="district" id="district" value="{{ @$company->district }}" onchange="fillZip();" disabled>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">Neighborhood</label>
                                            <?php $neighborhood = isset($company->neighborhood) ? $company->neighborhood : null; ?>
                                            {!! Form::text('neighborhood',$neighborhood,['placeholder' => 'Neighborhood','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('neighborhood'))
                                            <div class="error">{{ $errors->first('neighborhood') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">Zip</label>
                                            <?php $zip = isset($company->zip) ? $company->zip : null; ?>
                                            <input type="text" class="form-control" name="zip" id="zip" value="{{ @$zip }}" readonly >
                                            @if ($errors->has('zip'))
                                            <div class="error">{{ $errors->first('zip') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="Direccion">Direccion</label>
                                        <?php $address = isset($company->address) ? $company->address : null; ?>
                                        {!! Form::textarea('address',$address,['placeholder' => 'Address','id'=>'Direccion','rows' => 5, 'cols' => 40,'class' => 'form-control','readonly']) !!}
                                        @if ($errors->has('address'))
                                        <div class="error">{{ $errors->first('address') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="tab-pane fade" style="display:none" id="v-pills-certificate" role="tabpanel" aria-labelledby="v-pills-certificate-tab">
                            <div class="row">
                                <div class="col-md-8 col-sm-8">
                                    <div class="form-row">
                                        <div class="form-group{{ $errors->has('user_name') ? ' has-error' : '' }} col-md-6">
                                            <label for="inputTestl4">Usuario ATV</label>
                                            <?php $user_name = isset($certificate->user) ? $certificate->user : null; ?>
                                            {!! Form::text('user_name',$user_name,['placeholder' => 'Usuario ATV','id'=>'inputTestl4','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('user_name'))
                                            <div class="error">{{ $errors->first('user_name') }}</div>
                                            @endif

                                        </div>
                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} col-md-6">
                                            <label for="inputTestl5">Contraseña ATV</label>
                                            <?php $password = isset($certificate->password) ? $certificate->password : null; ?>
                                            {!! Form::text('password',$password,['placeholder' => 'Contraseña ATV','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('password'))

                                            <div class="error">{{ $errors->first('password') }}</div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputTestl5">PIN de llave criptográfica</label>
                                            <?php $pin = isset($certificate->pin) ? $certificate->pin : null; ?>
                                            {!! Form::text('pin',$pin,['placeholder' => 'PIN de llave criptográfica','id'=>'inputTestl5','class' => 'form-control','readonly']) !!}
                                            @if ($errors->has('pin'))

                                            <div class="error">{{ $errors->first('pin') }}</div>
                                            @endif

                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputText1">Llave criptográfica</label>
                                            <div class="">
                                            <div class="fallback">                                                
                                                <?php if(!empty($certificate->key_url)){?><a href="{{asset('atv_certificates/'.$certificate->key_url)}}" target="_blank">{{$certificate->key_url}}</a><?php }?>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" style="display:none" id="v-pills-team" role="tabpanel" aria-labelledby="v-pills-team-tab">
                                <div class="col-md-12 col-sm-12">
                                    <h3 class="card-title">Team Members of company "{{$team->name}}"</h3>
                                  <table id="dataTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                      <thead>
                                          <tr>
                                              <th>Name</th>
                                              <th>Email</th>
                                              <th>Action</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @if ( $team->users->count() )
                                          @foreach($team->users AS $user)
                                          <tr>
                                              <td>{{$user->first_name.' '.$user->last_name.' '.$user->last_name2}}</td>
                                              <td>{{$user->email}}</td>
                                              <td>
                                                  @if(auth()->user()->isOwnerOfTeam($team))
                                                  @if(auth()->user()->getKey() !== $user->getKey())
                                                  <form style="display: inline-block;" action="{{route('teams.members.destroy', [$team, $user])}}" method="post">
                                                      {!! csrf_field() !!}
                                                      @method('delete')
                                                      <button type="submit" class="text-danger mr-2" title="Delete" style="display: inline-block; background: none; border: 0;">
                                                          <i class="nav-icon i-Close-Window font-weight-bold"></i>
                                                      </button>
                                                  </form>
                                                  @endif
                                                  @endif
                                              </td>
                                          </tr>
                                          @endforeach
                                          @endif
                                      </tbody>
                                  </table>

                                  @if(auth()->user()->isOwnerOfTeam($team))
                                  <div class="col-md-12" style="padding: 2rem 0px;">
                                      <div class="car mb-4">
                                          <div class="car-body text-left">
                                              <h3 class="card-title">Pending invitations</h3>
                                              <div class="row">

                                                  <div class="col-sm-12">
                                                      <table class="table table-striped">
                                                          <thead>
                                                              <tr>
                                                                  <th>E-Mail</th>
                                                                  <th>Action</th>
                                                              </tr>
                                                          </thead>
                                                          @foreach($team->invites AS $invite)
                                                          <tr>
                                                              <td>{{$invite->email}}</td>
                                                              <td>
                                                                  <a href="{{route('teams.members.resend_invite', $invite)}}" class="btn btn-sm btn-default">
                                                                      <i class="fa fa-envelope-o"></i> Resend invite
                                                                  </a>
                                                              </td>
                                                          </tr>
                                                          @endforeach
                                                      </table>
                                                  </div>

                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="col-md-12" style="padding: 0px;">

                                      <div class="car mb-4">
                                          <div class="car-body text-left">
                                              <h3 class="card-title">Invite to team "{{$team->name}}"</h3>

                                              <form class="form-horizontal" method="post" action="{{route('teams.members.invite', $team)}}">
                                                  {!! csrf_field() !!}

                                                  <div class="row">
                                                      <div class="col-xs-6 col-sm-6 col-md-6">
                                                          <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                                              <strong>E-Mail Address *</strong>
                                                              {!! Form::text('email', old('email'), array('placeholder' => 'E-Mail Address','class' => 'form-control','required')) !!}

                                                              @if ($errors->has('email'))
                                                              <span class="help-block">
                                                                  <strong>{{ $errors->first('email') }}</strong>
                                                              </span>
                                                              @endif
                                                          </div>
                                                      </div>

                                                      <div class="form-group">
                                                          <div class="col-md-6 col-md-offset-4">
                                                              <button type="submit" class="btn btn-primary"><i class="fa fa-btn fa-envelope-o"></i>Invite to Team</button>
                                                          </div>
                                                      </div>
                                                  </div>

                                              </form>

                                          </div>
                                      </div>
                                  </div>
                                  @endif
                                </div>
                            </div>
                            <input type="hidden" id="is-company-edit" value="2">
                        <script>

                            $(document).ready(function() {
                            $('.nav > li > a').click(function(event){
                            event.preventDefault(); //stop browser to take action for clicked anchor

                            //get displaying tab content jQuery selector
                            var active_tab_selector = $('.nav > li.active > a').attr('href');
                            //alert(active_tab_selector);
                            //find actived navigation and remove 'active' css
                            var actived_nav = $('.nav > li.active');
                            actived_nav.removeClass('active');
                            //add 'active' css into clicked navigation
                            $(this).parents('li').addClass('active');

                            //hide displaying tab content
                            $(active_tab_selector).removeClass('active');
                            //$(active_tab_selector).removeClass('show');
                            $(active_tab_selector).css('display', 'none');
                            //show target tab content
                            var target_tab_selector = $(this).attr('href');
                            // $(target_tab_selector).removeClass('hide');
                            $(target_tab_selector).css('display', 'block');
                            });
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
        <!-- tabbable card end here -->

        @endsection

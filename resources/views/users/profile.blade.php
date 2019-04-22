@extends('layouts/app')
@section('title')
  Mi perfil
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
                                <li><a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">Visión general</a></li>
                                <li><a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Editar información personal</a></li>
                                <li><a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Seguridad</a></li>
                              </ul>
                            </div>
                            <div class="col-9">
                              <div class="tab-content p-0" id="v-pills-tabContent">

                                <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                    <div class="row">
                                    <div class="col-md-8 col-sm-8">
                                        <form>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                <label for="inputTestl4">Nombre :</label>
                                                <label for="inputTestl4"> {{Auth::user()->first_name}} </label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                <label for="inputText">Primer apellido :</label>
                                                  <label for="inputText">   {{Auth::user()->last_name}} </label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                <label for="inputTestl5">Segundo apellido :</label>
                                                <label for="inputTestl5">  {{Auth::user()->last_name2}} </label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                <label for="inputText1">Cedula :</label>
                                              <label for="inputText1"> {{Auth::user()->id_number }}</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                <label for="inputTestl5">Telefono :</label>
                                                <label for="inputTestl5"> {{Auth::user()->phone}}</label>
                                                </div>
                                                <div class="form-group col-md-6">
                                                <label for="inputText1">Celular :</label>
                                                  <label for="inputText1">  {{Auth::user()->celular}}</label>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                <label for="inputTestl5">Email :</label>
                                                <label for="inputTestl5"> {{Auth::user()->email}}</label>
                                                </div>
                                                
                                            </div>
                                            <div class="form-group">
                                                <label for="Direccion">Direccion :</label>
                                                  <label for="Direccion"> {{Auth::user()->address}}</label>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <form>
                                            <div class="form-row">
                                                <div class="form-group">
                                                <label for="inputTestl4">Estado :</label>
                                                  <label for="inputTestl4">  {{Auth::user()->state }}</label>
                                                </div>
                                            </div>
                                            <!--div class="form-row">
                                                <div class="form-group">
                                                <label for="inputTestl4">Perfil :</label>
                                                <label for="inputTestl4">  <?php //$fname = isset(Auth::user()->first_name) ?  Auth::user()->first_name : null; ?></label>
                                                </div>
                                            </div-->
                                        </form>
                                    </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                  {!! Form::model(Auth::user(),['route' => ['users.update_profile',Auth::user()->id],'method' => 'put','autocomplete' => 'off','files' => true]) !!}
                                <div class="row">
                                  <div class="col-md-8 col-sm-8">

                                          <div class="form-row">
                                              <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }} col-md-6">
                                              <label for="inputTestl4">First Name</label>
                                              <?php $fname = isset(Auth::user()->first_name) ?  Auth::user()->first_name : null; ?>
                                              {!! Form::text('first_name',$fname,['placeholder' => 'First Name','id'=>'inputTestl4','class' => 'form-control']) !!}
                                              @if ($errors->has('first_name'))
                                              <div class="error">{{ $errors->first('first_name') }}</div>
                                              @endif
                                              </div>
                                              <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }} col-md-6 ">
                                              <label for="inputText">First Surname</label>
                                              <?php $lname = isset(Auth::user()->last_name) ? Auth::user()->last_name : null; ?>
                                              {!! Form::text('last_name',$lname,['placeholder' => 'First Surname','id'=>'inputTest','class' => 'form-control']) !!}
                                              @if ($errors->has('last_name'))
                                              <div class="error">{{ $errors->first('last_name') }}</div>
                                              @endif
                                              </div>
                                          </div>
                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl5">second Surname</label>
                                              <?php $lname2 = isset(Auth::user()->last_name2) ? Auth::user()->last_name2 : null; ?>
                                              {!! Form::text('last_name2',$lname2,['placeholder' => 'Second Surname','id'=>'inputTestl5','class' => 'form-control']) !!}
                                              @if ($errors->has('last_name2'))
                                              <div class="error">{{ $errors->first('last_name') }}</div>
                                              @endif
                                              </div>
                                              <div class="form-group col-md-6">
                                              <label for="inputText1">Cedula</label>
                                              <?php $id_number = isset(Auth::user()->id_number) ? Auth::user()->id_number : null; ?>
                                              {!! Form::text('id_number',$id_number,['placeholder' => 'Identification Number','id'=>'inputTest1','class' => 'form-control']) !!}
                                              @if ($errors->has('id_number'))
                                              <div class="error">{{ $errors->first('id_number') }}</div>
                                              @endif
                                              </div>
                                          </div>
                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl5">Telefono</label>
                                              <?php $mobile = isset(Auth::user()->phone) ? Auth::user()->phone : null; ?>
                                              {!! Form::number('phone',$mobile,['placeholder' => 'Phone','id'=>'inputTestl5','class' => 'form-control']) !!}
                                              @if ($errors->has('phone'))
                                              <div class="error">{{ $errors->first('phone') }}</div>
                                              @endif
                                              </div>
                                              <div class="form-group col-md-6">
                                              <label for="inputText1">Celular</label>
                                              <?php $celular = isset(Auth::user()->celular) ? Auth::user()->celular : null; ?>
                                              {!! Form::text('celular',$celular,['placeholder' => 'Celular','id'=>'inputTest1','class' => 'form-control']) !!}
                                              @if ($errors->has('celular'))
                                              <div class="error">{{ $errors->first('celular') }}</div>
                                              @endif
                                              </div>
                                            </div>
                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl5">Email</label>
                                              <?php $email = isset(Auth::user()->email) ? Auth::user()->email : null; ?>
                                              {!! Form::text('email',$email,['placeholder' => 'email','id'=>'inputTestl5','class' => 'form-control']) !!}
                                              @if ($errors->has('email'))
                                              <div class="error">{{ $errors->first('email') }}</div>
                                              @endif
                                              </div>
                                              
                                          </div>
                                          <div class="form-group">
                                              <label for="Direccion">Direccion</label>
                                              <?php $address = isset(Auth::user()->address) ? Auth::user()->address : null; ?>
                                              {!! Form::textarea('address',$address,['placeholder' => 'Address','id'=>'Direccion','rows' => 5, 'cols' => 40,'class' => 'form-control']) !!}
                                              @if ($errors->has('address'))
                                              <div class="error">{{ $errors->first('address') }}</div>
                                              @endif
                                          </div>

                                    </div>

                                  <div class="col-md-4 col-sm-4">

                                          <div class="form-row">
                                              <div class="form-group">
                                              <label for="inputTestl4">Estado</label>
                                              <?php $state = isset(Auth::user()->state) ? Auth::user()->state : null; ?>
                                              {!! Form::text('state',$state,['placeholder' => 'state','id'=>'inputTestl4','class' => 'form-control']) !!}
                                              @if ($errors->has('state'))
                                              <div class="error">{{ $errors->first('state') }}</div>
                                              @endif
                                              </div>
                                          </div>
                                          <!--div class="form-row">
                                              <div class="form-group">
                                              <label for="inputTestl4">Perfil</label>
                                              <input type="text" class="form-control" id="inputTestl4" placeholder="">
                                            </div-->
                                          </div>
                                          <div class="col-md-8 col-sm-8 d-flex flex-wrap">
                                              <h6 class="w-100">By clicking UPDATE, you are updating your password information.</h6>
                                              <button class="btn btn-primary btn-sm btn-raised" type="submit">Update </button>
                                          </div>
                                  <input type="hidden" name="form_type" value="account-form">
                                </div>
                                  {!! Form::close() !!}
                                </div>

                                <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                  {!! Form::model(Auth::user(),['route' => ['users.update_profile',Auth::user()->id],'method' => 'put','autocomplete' => 'off','class'=>'mt-3']) !!}
                                  <div class="col-md-8 col-sm-8">

                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl4">Old Password</label>
                                              {!! Form::password('old_password', ['class' => 'form-control','placeholder' => 'Old Password']) !!}
                                              @if ($errors->has('old_password'))
                                              <div class="error">{{ $errors->first('old_password') }}</div>
                                              @endif
                                              </div>
                                          </div>
                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl5">New Password</label>
                                              {!! Form::password('password', ['class' => 'form-control','placeholder' => 'New Password']) !!}
                                              @if ($errors->has('password'))
                                              <div class="error ">{{ $errors->first('password') }}</div>
                                              @endif
                                              </div>
                                            </div>
                                          <div class="form-row">
                                              <div class="form-group col-md-6">
                                              <label for="inputTestl5">Confirm Password</label>
                                              {!! Form::password('confirm_password', ['class' => 'form-control','placeholder' => 'Confirm Password']) !!}
                                              @if ($errors->has('confirm_password'))
                                              <div class="error">{{ $errors->first('confirm_password') }}</div>
                                              @endif
                                              </div>
                                            </div>

                                  </div>
                                  <div class="col-md-8 col-sm-8 d-flex flex-wrap">
                                      <h6 class="w-100">By clicking UPDATE, you are updating your password information.</h6>
                                      <button class="btn btn-primary btn-sm btn-raised" type="submit">Update </button>
                                  </div>
                                    {!! Form::close() !!}
                                </div>


                              </div>
                            </div>
                          </div>
                    </div>
                    <!-- tabbable card end here -->

@endsection

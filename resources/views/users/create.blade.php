@extends('layouts/app')

@section('title')
Create User
@endsection

@section('content')


<form method="POST" action="{{ route('users.store') }}">

    @csrf    

    <div class="row">

        <div class="form-group col-md-12">
            <h3>
                User Information
            </h3>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>First Name *</strong>
                <input type="text" placeholder="First Name" name="first_name" class="form-control" value="{{old('first_name')}}">            
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Last Name</strong>
                <input type="text" placeholder="Last Name" name="last_name" class="form-control" value="{{old('last_name')}}">            
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>User Name *</strong>
                <input type="text" placeholder="User Name" name="user_name" class="form-control" value="{{old('user_name')}}">            
            </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Email *</strong>
                <input type="text" placeholder="Email" name="email" class="form-control" value="{{old('email')}}">                        
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Password *</strong>
                <input type="password" placeholder="Password" name="password" class="form-control">                                    
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Confirm Password *</strong>
                <input type="password" placeholder="Confirm Password" name="confirm-password" class="form-control">            
            </div>
        </div>

        <div class="col-xs-6 col-sm-6 col-md-6">
            <div class="form-group">
                <strong>Role *</strong>
                <select class="form-control" name="roles">
                    <option value="">Select Role</option>
                    @foreach($roles as $key => $val)
                    <option value="{{$key}}" <?php echo ($key == old('roles')) ? 'selected' : ''; ?>>{{$val}}</option>
                    @endforeach
                </select>            
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

@endsection

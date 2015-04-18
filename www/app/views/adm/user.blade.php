@extends('layouts.main')

@section('content')
  <div id="admin">

@if(Auth::user()->type == '0' or $user->parent_id == Auth::user()->id or $user->id == Auth::user()->id)

  <h2>{{ $user->name }}</h2>

  	@if($errors->has())
  	<div id="form-errors"> 
  	<p>Errors:</p>
  		<ul>
  			@foreach ($errors->all() as $error)
  				<li>{{ $error }}</li>
  			@endforeach
  		</ul>
  	</div>
  	@endif

  	{{ Form::open(array('url'=>'adm/users/update', 'class'=>'form-signin')) }}
    <div class="input-prepend">
    <span class="add-on"><i class="icon-user"></i></span>
  	{{ Form::text('name', $user->name, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'Full Name')) }}
    </div>
    <br />
    <div class="input-prepend">
    <span class="add-on"><i class="icon-envelope"></i></span>
  	{{ Form::text('email', $user->email, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'User Email')) }}
    </div>
    <br />
    <div class="input-prepend">
    <span class="add-on"><i class="icon-lock"></i></span>
  	{{ Form::text('password', null, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'User passowrd')) }}
    </div>
    @if(Auth::user()->type == '0')
    <div class="input-prepend">
    <span class="add-on"><i class="icon-tag"></i></span>
      {{ Form::select('type', array('A' => 'Admin', '1' => 'Agent', '2' => 'User'), $user->type, array('class'=>'input-xlarge')) }}
    </div>
    <div class="input-prepend">
    <span class="add-on"><i class="icon-user"></i></span>
    {{ Form::select('parent_id', SwitchUser::lists('name', 'id'), $user->parent_id, array('class'=>'input-xlarge')) }}
    </div>
    @endif
    {{ Form::hidden('id', $user->id) }}
  	{{ Form::submit('Update user profile', array('class'=>'btn btn-large btn-primary btn-block')) }}
  	{{ Form::close() }}

@if(Auth::user()->type == '0')
  {{ Form::open(array('url'=>'adm/users/destroy', 'class'=>'form-signin')) }}
	{{ Form::hidden('id', $user->id) }}
	{{ Form::submit('Delete user', array('class'=>'btn btn-large btn-danger btn-block')) }}
	{{ Form::close() }}
@endif

@if($user->users->count() > 0)
    <h3>{{ $user->users->count() }} users belong to {{ $user->name }}</h3>
    <table class="table table-hover">
      <thead>
    <tr>
      <th>id</th>
      <th>Name</th>
      <th>Email</th>
      <th>Type</th>
      <th>OpenVPN</th>
    </tr>
  </thead>
    <tbody>
  @foreach ($user->users as $u)
      <tr class="{{ $u->getTypeClass() }}">
      <td>{{ $u->id }}</td>
      <td><a href="/adm/user/{{ $u->id }}"> {{ $u->name }}</a></td>
      <td>{{ $u->email }}</td>
      <td>{{ $u->getType() }}</td>
      <td>
      @if(file_exists ( "keys/SwitchUser_".$u->id.".zip" ))
        <a class="btn btn-mini btn-success" href="/keys/SwitchUser_{{ $u->id }}.zip"><i class="icon-download icon-white"></i></a>
      @endif
      </td>
      </tr>
  @endforeach
  </tbody>
  </table>
@endif

  </div>

@endif
@stop

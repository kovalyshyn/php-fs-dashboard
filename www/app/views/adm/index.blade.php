@extends('layouts.main')

@section('content')
  <div id="admin">

  	<h2>Users List</h2>
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
	@foreach ($users as $user)
			<tr class="{{ $user->getTypeClass() }}"><td>{{ $user->id }}</td>
      <td><a href="/adm/user/{{ $user->id }}"> {{ $user->name }}</a></td>
      <td>{{ $user->email }}</td>
      <td>{{ $user->getType() }}
      @if($user->parent)
      - belongs to <a href="/adm/user/{{ $user->parent->id }}">{{ $user->parent->name }}</a>
      @endif
      </td>
      <td>
      @if(file_exists ( "keys/SwitchUser_".$user->id.".zip" ))
        <a class="btn btn-mini btn-success" href="/keys/SwitchUser_{{ $user->id }}.zip"><i class="icon-download icon-white"></i></a>
      @endif
      </td>
      </tr>
	@endforeach
  </tbody>
  </table>

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

<hr />
  	{{ Form::open(array('url'=>'adm/users/create', 'class'=>'form-signin')) }}
  	<div class="input-prepend">
    <span class="add-on"><i class="icon-user"></i></span>
    {{ Form::text('name', null, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'Full Name')) }}
    </div>
    <br />
    <div class="input-prepend">
    <span class="add-on"><i class="icon-envelope"></i></span>
    {{ Form::text('email', null, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'User Email')) }}
    </div>
    <br />
    <div class="input-prepend">
    <span class="add-on"><i class="icon-lock"></i></span>
    {{ Form::text('password', null, array('class'=>'input-block-level input-xlarge', 'placeholder'=>'User passowrd')) }}
    </div>
     <br />
    <div class="input-prepend">
    <span class="add-on"><i class="icon-tag"></i></span>
    @if(Auth::user()->type == '0')
      {{ Form::select('type', array('0' => 'Admin', '1' => 'Agent', '2' => 'User'), array('class'=>'input-block-level input-xlarge')) }}
    @else 
      {{ Form::select('type', array('2' => 'User'), array('class'=>'input-block-level input-xlarge')) }}
    @endif
    </div>
    {{ Form::hidden('parent_id', Auth::user()->id) }}
  	{{ Form::submit('Create new user', array('class'=>'btn btn-large btn-primary btn-block')) }}
  	{{ Form::close() }}

  </div>

@stop
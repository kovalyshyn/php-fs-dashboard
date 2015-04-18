@extends('layouts.main')

@section('content')
  <div id="admin">

<h2>Blacklist Manager</h2>

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

<div class="btn-group">
  <a class="btn btn-primary" href="/blacklist/"><i class="icon-random icon-white"></i> Select destination</a>
  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
  <ul class="dropdown-menu">
  @foreach ($destinations as $d)
    <li><a href="/blacklist/show/{{ $d->id }}"><i class="{{ $d->getActiveIcon() }}"></i> {{ $d->name }}</a></li>
  @endforeach
  </ul>
</div>

  @yield('blacklist')

  </div>

@stop
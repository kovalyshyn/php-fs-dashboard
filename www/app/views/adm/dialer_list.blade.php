@extends('layouts.main')

@section('content')

  <a class="btn btn-mini" href="/adm/dialer/new"><i class="icon-file"></i>new Task</a>
  <hr class="container">

  @foreach ($dialer as $msg)
      <small>
      @if($msg->done)
        <i class="icon-ok"></i>
      @else
        <i class="icon-repeat"></i>
        <a class="icon-remove" href="/adm/dialer/cancel/{{ $msg->id }}"></a>
        
      @endif
      {{ $msg->created_at }} [ {{ $msg->durations }} / {{ $msg->concurrent_calls}} / {{ $msg->total_calls }}
      @if($msg->wait_answer)  
        / SIP 200OK 
      @endif
      ]
      </small>
    @if($msg->done)
      <div class="control-group success">
    @else
      <div class="control-group info">
    @endif
    <span class="help-inline">
    From {{ $msg->source_num }} via {{ $msg->destination_srv }}
    </span>
    </div>

      @if(!$msg->done and $msg->started_at)
        <div class="control-group warning"> 
	<span class="help-inline">
	started at {{ $msg->started_at }} 
	</span></div>
      @endif
  
  @endforeach

<?php echo $dialer->links(); ?>
<hr class="container">

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

@stop

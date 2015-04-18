@extends('layouts.main')

@section('content')
  <div id="admin">

@if(Auth::user()->type == '0' or $getaway->parent_id == Auth::user()->id or $getaway->user_id == Auth::user()->id)

  <h2>Gateway {{ $getaway->id }} 
  @if($getaway->type_id == '2')
    <a class="btn btn-mini btn-inverse" href="/getaways/cfg/{{ $getaway->id }}"><i class="icon-download-alt icon-white"></i></a>
  @endif
  </h2>
  <h5>Today connected 
  {{ round($getaway->cdr()->whereRaw('date("start_stamp") = DATE \'today\'')->where('gw_id', '=', $getaway->id)->sum('billsec')/60, 2) }} minutes</h5>
  <h5>Last Hangup: {{ $getaway->last_hangup_cause }}</h5>

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

  	{{ Form::open(array('url'=>'getaways/update', 'class'=>'form-signin')) }}
    @if(Auth::user()->type == '0')
    {{ Form::select('destinations', Destinations::where('active', '=', 1)->lists('name', 'id'), $getaway->destinations, array('class'=>'input-block-level')) }}
    @else 
    {{ Form::select('destinations', Destinations::where('user_id', '=', Auth::user()->id)->lists('name', 'id'), $getaway->destinations, array('class'=>'input-block-level')) }}
    @endif
    @if($getaway->type_id == '2')
      {{ Form::text('imei', $getaway->imei, array('class'=>'input-block-level', 'placeholder'=>'SIP-GSM IMEI')) }}
    @endif
    {{ Form::text('ip', $getaway->ip, array('class'=>'input-block-level', 'placeholder'=>'Gateway IP')) }}
    {{ Form::text('port', $getaway->port, array('class'=>'input-block-level', 'placeholder'=>'Gateway SIP Port')) }}
    @if($getaway->type_id == '1')
      {{ Form::text('mask', $getaway->mask, array('class'=>'input-block-level', 'placeholder'=>'GoIP mask')) }}
    @endif
    @if(Auth::user()->type == '0')
    	@if($getaway->type_id == '3')
		{{ Form::label('bridge_string', 'FreeSWITCH dial string:') }}
        	{{ Form::text('bridge_string', $getaway->bridge_string, array('class'=>'input-block-level')) }}
		{{ Form::label('call_timeout', 'FreeSWITCH call timeout:') }}
                {{ Form::text('call_timeout', $getaway->call_timeout, array('class'=>'input-block-level')) }}
	@endif
      {{ Form::select('user_id', SwitchUser::lists('name', 'id'), $getaway->user_id, array('class'=>'input-block-level')) }}
      {{ Form::select('sip_profile', SipProfile::lists('name', 'name'), $getaway->sip_profile, array('class'=>'input-block-level')) }}
    @else 
      {{ Form::hidden('sip_profile', $getaway->sip_profile) }}
    @endif
    @if($getaway->type_id == '3')
      {{ Form::label('concurrent', 'Concurrent Calls:') }}
      {{ Form::text('concurrent', $getaway->concurrent, array('class'=>'input-block-level')) }}
    @endif
    {{ Form::label('limit', 'Maximum Success Calls per Day:') }}
    {{ Form::text('limit', $getaway->limit, array('class'=>'input-block-level')) }}
    {{ Form::label('minutes', 'Maximum Minutes allow per Day:') }}
    {{ Form::text('minutes', $getaway->minutes, array('class'=>'input-block-level')) }}
    <label class="checkbox">{{ Form::checkbox('delay_rnd', '1', $getaway->delay_rnd) }} Random:</label>
    <div class="input-block-level">
    {{ Form::text('delay_from', $getaway->delay_from, array('placeholder'=>'From')) }}
    {{ Form::text('delay_to', $getaway->delay_to, array('placeholder'=>'To')) }}
    </div>
    <div class="input-block-level">
    <label>Delay seconds between Success Calls or {{ Form::checkbox('delay_all', '1', $getaway->delay_all) }} any calls:</label>
    {{ Form::text('delay', $getaway->delay, array('class'=>'input-block-level')) }}
    </div>
    <label class="checkbox">{{ Form::checkbox('active', '1', $getaway->active ) }} Active</label>
    {{ Form::hidden('id', $getaway->id) }}
    @if($getaway->Registration()->count() > 0 )
    <label class="control-label"><strong>Registred:</strong> {{ $getaway->Registration->url }}</label>
    @endif


    {{ Form::submit('Update Gateway', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}

@if(Auth::user()->type == '0')
  {{ Form::open(array('url'=>'getaways/destroy', 'class'=>'form-signin')) }}
	{{ Form::hidden('id', $getaway->id) }}
	{{ Form::submit('Delete Gateway', array('class'=>'btn btn-large btn-danger btn-block')) }}
	{{ Form::close() }}
@endif

<H3>CDR</H3>

@if($cdr->count() >0)
    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th>start_stamp</th>
      <th>caller_id_number</th>
      <th>destination_number</th>
      <th>duration</th>
      <th>billsec</th>
      <th>rate</th>
      <th>codec</th>
      <th>hangup_cause</th>
    </tr>
  </thead>
    <tbody>
    @foreach ($cdr as $c)
      <tr 
      @if($c->billsec >0)
      class="success"
      @endif
      >
      <td>{{ $c->start_stamp }}</td>
      <td>{{ $c->caller_id_number }}</td>
      <td>{{ $c->destination_number }}</td>
      <td>{{ $c->duration }}</td>
      <td>{{ $c->billsec }}</td>
      <td>
        @if (Auth::user()->type == '0')
          {{ $c->rate_admin }}
        @elseif (Auth::user()->type == '1')
          {{ $c->rate_agent }}
        @else
          {{ $c->rate_user }}
        @endif
      </td>
      <td>{{ $c->read_codec }}</td>
      <td>{{ $c->hangup_cause }}</td>
      </tr>
    @endforeach
  </tbody>
  </table>

<?php echo $cdr->links(); ?>

@endif

  </div>

@endif
@stop

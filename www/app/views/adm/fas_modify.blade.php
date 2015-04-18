@extends('layouts.main')

@section('content')

@if(Auth::user()->type == '0')
{{ Form::open(array('url'=>'adm/fas/update', 'class'=>'form-signin', 'files' => true )) }}

  <h3>{{ Form::checkbox('active', '1', $dest->active) }} {{ $dest->name }}</h3>

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

    <h5>Today connected {{ $dest->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('context', '=', 'fas')->sum('billsec')}} seconds, AVG {{ round($dest->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('context', '=', 'fas')->avg('billsec'), 2) }} sec, {{ $dest->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('context', '=', 'fas')->count()}} calls</h5>
    <h5>All connected {{ $dest->getCDR()->where('context', '=', 'fas')->sum('billsec')}} seconds, AVG {{ round($dest->getCDR()->where('context', '=', 'fas')->avg('billsec'), 2) }} sec, {{ $dest->getCDR()->where('context', '=', 'fas')->count()}} calls</h5>
    
    <h6>Destination setup</h6>
    
    {{ Form::text('name', $dest->name, array('class'=>'input-block-level', 'placeholder'=>'Destination Name')) }}
    {{ Form::text('global_prefix', $dest->global_prefix, array('class'=>'input-block-level', 'placeholder'=>'Global prefix')) }}
    {{ Form::text('number_length', $dest->number_length, array('class'=>'input-block-level', 'placeholder'=>'Number Length without prefix')) }}
    {{ Form::text('tone_stream', $dest->tone_stream, array('class'=>'input-block-level', 'placeholder'=>'FreeSWITCH Tone Streem')) }}
    <h6>Call setup</h6>
    <label class="checkbox">{{ Form::text('before_ansfer', $dest->before_ansfer) }} Static PDD (ms)</label>
    <label class="checkbox">{{ Form::checkbox('random_pdd', '1', $dest->random_pdd) }} Random PDD (ms):</label>
    <label class="checkbox">{{ Form::text('before_ansfer_from', $dest->before_ansfer_from, array('placeholder'=>'From')) }}</label>
    <label class="checkbox">{{ Form::text('before_ansfer_to', $dest->before_ansfer_to, array('placeholder'=>'To')) }}</label>
    <label class="checkbox">{{ Form::text('after_ansfer', $dest->after_ansfer) }} Pause (ms)</label>

    <h6>Recording</h6>
    {{ Form::text('tone_stream_duration', $dest->tone_stream_duration, array('class'=>'input-block-level', 'placeholder'=>'Number copies of the FreeSWITCH tone stream')) }}
    <p>Load MP3 file {{ Form::file('recording_file') }} <u>{{ $dest->recording_file }}</u></p>
    
    {{ Form::hidden('id', $dest->id) }}
    {{ Form::submit('Update destination', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}
    
    {{ Form::open(array('url'=>'adm/fas/destroy', 'class'=>'form-signin')) }}
    {{ Form::hidden('id', $dest->id) }}
    {{ Form::submit('Delete destination', array('class'=>'btn btn-large btn-danger btn-block')) }}
    {{ Form::close() }}

    {{ Form::open(array('url'=>'adm/fas/update', 'class'=>'form-inline')) }}
  {{ Form::text('rate_user', $dest->rate_user, array('class'=>'input-small', 'placeholder'=>"Users rate")) }}
  {{ Form::text('rate_agent', $dest->rate_agent, array('class'=>'input-small', 'placeholder'=>"Agents rate")) }}
  {{ Form::text('rate_admin', $dest->rate_admin, array('class'=>'input-small', 'placeholder'=>"Admins rate")) }}
  {{ Form::hidden('rate', true) }}
  {{ Form::hidden('id', $dest->id) }}
  {{ Form::submit('Set rate', array('class'=>'btn btn-success')) }}
  {{ Form::close() }}


<H3>Last 100 records</H3>

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

@endif


@endif

@stop
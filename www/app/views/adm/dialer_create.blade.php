@extends('layouts.main')

@section('content')

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

{{ Form::open(array('url'=>'/adm/dialer/create', 'class'=>'form-signin', 'files' => true )) }}
{{ Form::label('destination_srv', 'SIP URI: ') }}
{{ Form::text('destination_srv', '178.238.234.101:5060') }}
{{ Form::label('source_num', 'Source Number: ') }}
{{ Form::text('source_num', '78001234567') }}
{{ Form::label('durations', 'Call duration: ') }}
{{ Form::text('durations', '60') }} seconds.
{{ Form::label('concurrent_calls', 'Concurrent calls: ') }}
{{ Form::text('concurrent_calls', '6') }}
{{ Form::label('total_calls', 'Total rounds: ') }}
{{ Form::text('total_calls', '10') }}
{{ Form::label('pause_between_rounds', 'Pause between rounds: ') }}
{{ Form::text('pause_between_rounds', '10') }} seconds.
<p>Load B numbers: {{ Form::file('b_numbers_file') }}</p>
<p><hr />
Generate B numbers:</p> 
Mask:  {{ Form::text('b_numbers_mask', null) }}<br /> 
Count: {{ Form::text('b_numbers_count', null) }} 
<hr /></p>
<label class="checkbox">{{ Form::checkbox('wait_answer', '1', true ) }} Wait for Answer</label>
{{ Form::submit('Create new task', array('class'=>'btn btn-success btn-block')) }}
{{ Form::close() }}

@stop

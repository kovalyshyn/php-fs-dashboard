@extends('layouts.main')

@section('content')
  <div id="admin">
  @if(Auth::user()->type == '0')

<a class="btn btn-success" href="/adm/fas/callflow"><i class="icon-tasks icon-white"></i> Callflow</a>

    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th>Status</th>
      <th>Name</th>
      <th>Global prefix</th>
      <th>Without prefix</th>
      <th>Tone</th>
      <th><abbr title="Before answer ms / After answer ms ">Answer ms</abbr></th>
      <th>Today connected sec</th>
      <th>Today avg sec</th>
    </tr>
  </thead>
    <tbody class="table-hover">
    @foreach ($dest as $d)
      <tr><td><i class="{{ $d->getActiveIcon() }}"></i></td><td>
      {{ Form::open(array('url'=>'adm/fas/modify', 'class'=>'form-inline')) }}
      {{ Form::hidden('id', $d->id) }}
      {{ Form::submit($d->name, array('class'=>'btn btn-link')) }}
      {{ Form::close() }}
      </td>
      <td>{{ $d->global_prefix }}</td>
      <td>{{ $d->number_length }}</td>
      <td>{{ $d->tone_stream }}</td>
      <td>
      {{ $d->before_ansfer }} / {{ $d->after_ansfer }}
      </td>
      <td>{{ $d->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('context', '=', 'fas')->sum('billsec')}}</td>
      <td>{{ round($d->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('context', '=', 'fas')->avg('billsec'), 2) }}</td>
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
    {{ Form::open(array('url'=>'adm/fas/create', 'class'=>'form-signin')) }}
    {{ Form::text('name', null, array('class'=>'input-block-level', 'placeholder'=>'Destinations Name')) }}
    {{ Form::text('global_prefix', null, array('class'=>'input-block-level', 'placeholder'=>'Global prefix')) }}
    {{ Form::text('number_length', null, array('class'=>'input-block-level', 'placeholder'=>'Number Length without prefix')) }}
    {{ Form::text('before_ansfer', null, array('class'=>'input-block-level', 'placeholder'=>'ms pause before Answer')) }}
    {{ Form::text('after_ansfer', null, array('class'=>'input-block-level', 'placeholder'=>'ms pause after Ansfer before tone')) }}
    {{ Form::text('tone_stream', null, array('class'=>'input-block-level', 'placeholder'=>'FreeSWITCH Tone Streem')) }}
    <label class="checkbox">{{ Form::checkbox('active', '1', true) }} Active</label>
    {{ Form::submit('Create new destinations', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}


  </div>
@endif

@stop
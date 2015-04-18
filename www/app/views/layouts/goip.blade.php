@extends('layouts.main')

@section('content')
  <div id="admin">

@if(Auth::check())
    <h2>new GoIP gateway</h2>

    {{ Form::open(array('url'=>'getaways/create', 'class'=>'form-signin')) }}
    {{ Form::select('destinations', Destinations::where('active', '=', 1)->lists('name', 'id'), Input::old('destinations'), array('class'=>'input-block-level')) }}
    {{ Form::hidden('user_id', Auth::user()->id) }}
    {{ Form::hidden('parent_id', Auth::user()->parent_id) }}
    {{ Form::hidden('type_id', '1') }}
    {{ Form::text('ip', null, array('class'=>'input-block-level', 'placeholder'=>'Gateway IP')) }}
    {{ Form::text('mask', null, array('class'=>'input-block-level', 'placeholder'=>'GoIP mask')) }}
    {{ Form::text('port', null, array('class'=>'input-block-level', 'placeholder'=>'Gateway SIP Port')) }}
    {{ Form::text('limit', null, array('class'=>'input-block-level', 'placeholder'=>'Maximum Success Calls per Day')) }}
    {{ Form::text('minutes', null, array('class'=>'input-block-level', 'placeholder'=>'Maximum Minutes allow per Day')) }}
    {{ Form::text('delay', null, array('class'=>'input-block-level', 'placeholder'=>'Delay seconds between Success Calls')) }}
    <label class="checkbox">{{ Form::checkbox('active', '1', true) }} Active</label>
    {{ Form::submit('Create new getaway', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}

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

</div>

@endif
@stop
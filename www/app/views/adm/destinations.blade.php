@extends('layouts.main')

@section('content')
  <div id="admin">

    <h2>Destinations</h2>
    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th>Name</th>
      <th>Global prefix</th>
      <!-- <th>Local prefix</th> -->
      <th>Without prefix</th>
      <th>Owner</th>
      <th>Status</th>
      <th>Getaways</th>
    </tr>
  </thead>
    <tbody class="table-hover">
    @foreach ($dest as $d)
        <tr><td>
            @if(Auth::user()->type == '0')
                {{ Form::open(array('url'=>'destinations/modify', 'class'=>'form-inline')) }}
                {{ Form::hidden('id', $d->id) }}
                {{ Form::submit($d->name, array('class'=>'btn btn-link')) }}
                {{ Form::close() }}
            @else 
            {{ $d->name }}
            @endif 
      </td>
      <td>{{ $d->global_prefix }}</td>
      <!-- <td>{{ $d->local_prefix }}</td> -->
      <td>{{ $d->number_length }}</td>
      <td>{{ ($d->user ? $d->user->name : null) }}</td>
      <td><i class="{{ $d->getActiveIcon() }}"></i> <a href="/blacklist/show/{{ $d->id }}"><i class="icon-list"></i></a></td>
      <td>{{ $d->getaways()->where('active', '=', '1')->count() }} / {{ $d->getaways->count() }}</td>
      </tr>
    @endforeach
  </tbody>
  </table>

<?php echo $dest->links(); ?>

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

@if(Auth::user()->type == '0')
<hr />
    {{ Form::open(array('url'=>'destinations/create', 'class'=>'form-signin')) }}
    {{ Form::text('name', null, array('class'=>'input-block-level', 'placeholder'=>'Destinations Name')) }}
    {{ Form::select('user_id', SwitchUser::lists('name', 'id'), Auth::user()->id, array('class'=>'input-block-level')) }}
    {{ Form::text('global_prefix', null, array('class'=>'input-block-level', 'placeholder'=>'Global prefix')) }}
    <!-- {{ Form::text('local_prefix', null, array('class'=>'input-block-level', 'placeholder'=>'Local prefix')) }} -->
    {{ Form::text('number_length', null, array('class'=>'input-block-level', 'placeholder'=>'Number Length without prefix')) }}
    <label class="checkbox">{{ Form::checkbox('active', '1', true) }} Active</label>
    {{ Form::submit('Create new destinations', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}
@endif

  </div>

@stop
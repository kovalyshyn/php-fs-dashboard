@extends('layouts.main')

@section('content')

@if(Auth::user()->type == '0')

  <h2>{{ $dest->name }}</h2>
  <h5><a href="/blacklist/show/{{ $dest->id }}"><i class="icon-list"></i></a> Today connected 
  {{ round($dest->getCDR()->whereRaw('date("start_stamp") = DATE \'today\'')->where('destination_id', '=', $dest->id)->sum('billsec')/60, 2) }} minutes</h5>
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

    {{ Form::open(array('url'=>'destinations/update', 'class'=>'form-signin')) }}
    {{ Form::text('name', $dest->name, array('class'=>'input-block-level', 'placeholder'=>'Destination Name')) }}
    {{ Form::select('user_id', SwitchUser::lists('name', 'id'), $dest->user_id, array('class'=>'input-block-level')) }}
    {{ Form::text('agent_prefix', $dest->agent_prefix, array('class'=>'input-block-level', 'placeholder'=>'Agent prefix')) }}
    {{ Form::text('global_prefix', $dest->global_prefix, array('class'=>'input-block-level', 'placeholder'=>'Global prefix')) }}
    <!-- {{ Form::text('local_prefix', $dest->local_prefix, array('class'=>'input-block-level', 'placeholder'=>'Local prefix')) }} -->
    {{ Form::text('number_length', $dest->number_length, array('class'=>'input-block-level', 'placeholder'=>'Number Length without prefix')) }}
    {{ Form::text('show_getaways', $dest->show_getaways, array('class'=>'input-block-level', 'placeholder'=>'Getaways selected at once')) }}
    {{ Form::text('ussd_balance', $dest->ussd_balance, array('class'=>'input-block-level', 'placeholder'=>'USSD: check balance')) }}
    {{ Form::text('ussd_balance_pattern', $dest->ussd_balance_pattern, array('class'=>'input-block-level', 'placeholder'=>'USSD: check balance regexp patern')) }}
    <label class="checkbox">{{ Form::checkbox('del_prefix', '1', $dest->del_prefix) }} Remove global prefix</label>
    <label class="checkbox">{{ Form::checkbox('active', '1', $dest->active) }} Active</label>
    {{ Form::hidden('id', $dest->id) }}

    {{ Form::submit('Update destination', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}
    
    {{ Form::open(array('url'=>'destinations/destroy', 'class'=>'form-signin')) }}
    {{ Form::hidden('id', $dest->id) }}
    {{ Form::submit('Delete destination', array('class'=>'btn btn-large btn-danger btn-block')) }}
    {{ Form::close() }}

  {{ Form::open(array('url'=>'destinations/update', 'class'=>'form-inline')) }}
  {{ Form::text('rate_user', $dest->rate_user, array('class'=>'input-small', 'placeholder'=>"Users rate")) }}
  {{ Form::text('rate_agent', $dest->rate_agent, array('class'=>'input-small', 'placeholder'=>"Agents rate")) }}
  {{ Form::text('rate_admin', $dest->rate_admin, array('class'=>'input-small', 'placeholder'=>"Admins rate")) }}
  {{ Form::hidden('rate', true) }}
  {{ Form::hidden('id', $dest->id) }}
  {{ Form::submit('Set rate', array('class'=>'btn btn-success')) }}
  {{ Form::close() }}

@if($dest->getaways->count() > 0)
    {{ Form::open(array('url'=>'getaways/up/'.$dest->id)) }}
    <h3>{{ $dest->getaways->count() }} gateways belong to {{ $dest->name }}</h3>
    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th></th>
      <th>id</th>
      <th>gateway</th>
      <th>owner</th>
      <th>limit</th>
      <th>minutes</th>
      <th>delay</th>
      <th>balance</th>
      <th>last connected at</th>
      <th>last hangup cause</th>
      <th><abbr title="Today AVG connected seconds">ACD</abbr></th>
    </tr>
  </thead>
    <tbody>
  @foreach ($dest->getaways as $gw)
      <tr><td>
      <input tabindex="1" type="checkbox" name="IDs[{{$gw->id}}]" id="{{$gw->id}}" value="{{$gw->id}}">
      @if($gw->Registration()->count() > 0 )
        <abbr title="{{ $gw->Registration->url }}"><i class="icon-ok-circle"></i></abbr>
      @endif
      </td>
      <td>
      @if($gw->active == 0 )
        <a href="/getaways/enable/{{ $gw->id }}/{{ $dest->id }}">
      @else
        <a href="/getaways/disable/{{ $gw->id }}/{{ $dest->id }}">
      @endif
      <i class="{{ $gw->getActiveIcon() }}"></i></a> 
      <a href="/getaways/show/{{ $gw->id }}"> {{ $gw->id }}</a></td>
      <td>{{($gw->mask? $gw->mask.":" : null)}}{{ $gw->ip }}</td>
      <td><a href="/adm/user/{{ $gw->user->id }}"> {{ $gw->user->name }}</a></td>
      <td>{{ $gw->limit }}</td>
      <td>{{ $gw->minutes }}</td>
      <td>{{ $gw->delay }}</td>
      <td>{{ ($gw->getBalance->first() ? $gw->getBalance()->orderBy('created_at', 'desc')->first()->balance : null)}}
      </td>
      <td>{{ date("Y-m-d H:i:s", strtotime($gw->connected)) }}</td>
      <td>{{ $gw->last_hangup_cause }}</td>
      <td>
      {{ round ($gw->cdr()->whereRaw('date("start_stamp") = DATE \'today\' AND "billsec" >0')->avg('billsec') ) }}
      </td>
      </tr>
  @endforeach
  </tbody>
  </table>
  {{ Form::submit('Enable \ Disable', array('class'=>'btn btn-warning')) }}
  {{ Form::close() }}


  {{ Form::open(array('url'=>'destinations/update', 'class'=>'form-inline')) }}
  {{ Form::text('limit', null, array('placeholder'=>"Maximum Success Calls per Day")) }}
  {{ Form::text('minutes', null, array('placeholder'=>"Maximum Minutes allow per Day")) }}
  {{ Form::text('delay', null, array('placeholder'=>"Delay seconds between Success Calls")) }}
  {{ Form::hidden('gw', true) }}
  {{ Form::hidden('id', $dest->id) }}
  {{ Form::submit('Apply to All Getaways', array('class'=>'btn btn-warning')) }}
  {{ Form::close() }}

@endif

@endif

@stop

@extends('layouts.main')

@section('content')
  <div id="admin">


  <div class="btn-group">
  <a class="btn btn-success" href="#"><i class="icon-print icon-white"></i> reports</a>
  <a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="/getaways/callflow"><i class="icon-tasks"></i> Callflow</a></li>
    <li><a href="#"><i class="icon-file"></i> Billing</a></li>
  </ul>
</div>

<div class="btn-group">
  <a class="btn btn-primary" href="#"><i class="icon-random icon-white"></i> add new getaway</a>
  <a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
  <ul class="dropdown-menu">
    <li><a href="/gw/1"><i class="icon-asterisk"></i> GoIP</a></li>
    <li><a href="/gw/2"><i class="icon-hdd"></i> Sip-GSM</a></li>
    <li><a href="/gw/3"><i class="icon-screenshot"></i> Default SIP</a></li>
  </ul>
</div>


@if($getaways->count() >0)
    {{ Form::open(array('url'=>'getaways/up')) }}
    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th></th>
      <th>id</th>
      <th>gateway</th>
      <th>destination</th>
      <th>user</th>
      <th>limit</th>
      <th><abbr title="Today connected minutes">minutes</abbr></th>
      <th>delay</th>
      <th>balance</th>
      <th>last used at</th>
      <th>{{ HTML::link('https://wiki.freeswitch.org/wiki/Hangup_Causes', 'last hangup cause') }}</th>
      <th><abbr title="Today AVG connected seconds">ACD</abbr></th>
    </tr>
  </thead>
    <tbody>
    @foreach ($getaways as $getaway)
      <tr class="{{ $getaway->getActiveClass() }}">
      <td>
      <input tabindex="1" type="checkbox" name="IDs[{{$getaway->id}}]" id="{{$getaway->id}}" value="{{$getaway->id}}">
      @if($getaway->Registration()->count() > 0 )
        <abbr title="{{ $getaway->Registration->url }}"><i class="icon-ok-circle"></i></abbr>
      @endif
      </td>
      <td>{{ $getaway->id }}</td>
      <td>
      @if($getaway->active == 0 )
        <a href="/getaways/enable/{{ $getaway->id }}">
      @else
        <a href="/getaways/disable/{{ $getaway->id }}">
      @endif
      <i class="{{ $getaway->getActiveIcon() }}"></i></a> 
      <a href="/getaways/show/{{ $getaway->id }}">{{($getaway->mask? $getaway->mask.":" : null)}}{{ $getaway->ip }}</a> ({{ $getaway->type->name }})</td>
      <td>{{ $getaway->destination->name }}</td>
      <td><a href="/adm/user/{{ $getaway->user_id }}">{{ $getaway->user->name }}</a></td>
      <td>{{ $getaway->limit }}</td>
      <td>{{ $getaway->minutes }} <small><span class="muted">{{round($getaway->cdr()->whereRaw('date("start_stamp") = DATE \'today\'')->where('gw_id', '=', $getaway->id)->sum('billsec')/60)}}</span></small></td>
      <td>{{ $getaway->delay }}</td>
      <td>
      @if($getaway->type->id == '1' )
        <a class="btn btn-mini btn-warning" href="http://{{ $_SERVER["SERVER_ADDR"] }}:82/goip_ussd.php?id={{ $getaway->id }}"><i class="icon-refresh"></i></a>
      @endif
      @if($getaway->type->id == '2000000000000000' )
        <a class="btn btn-mini btn-warning" href="http://{{ $_SERVER["SERVER_ADDR"] }}:82/gsm_ussd.php?id={{ $getaway->id }}"><i class="icon-refresh"></i></a>
      @endif
      {{ ($getaway->getBalance->first() ? $getaway->getBalance()->orderBy('created_at', 'desc')->first()->balance : null)}}
      </td>
      <td>{{ date("Y-m-d H:i", strtotime($getaway->selected)) }}</td>
      <td>
      {{ $getaway->last_hangup_cause }}
      </td>
      <td>
       {{ round ($getaway->cdr()->whereRaw('date("start_stamp") = DATE \'today\' AND "billsec" >0')->avg('billsec') ) }}
      </td>
      </tr>
    @endforeach
  </tbody>
  </table>

<?php echo $getaways->links(); ?>

    {{ Form::submit('Enable \ Disable', array('class'=>'btn btn-warning')) }}
    {{ Form::close() }}

@endif

@if(Auth::user()->type == '0')
<div class="text-right">
  {{ Form::open(array('url'=>'getaways', 'class'=>'btn-group')) }}
    {{ Form::select('destinations', Destinations::lists('name', 'id'), Input::get('destinations'), array('class'=>'btn btn-info dropdown-toggle', 'data-toggle'=>'dropdown')) }}
    {{ Form::hidden('do', 'filter') }}
    {{ Form::submit('>>', array('class'=>'btn btn-info dropdown-toggle')) }}
    {{ Form::close() }}
</div>
@endif

</div>

@stop
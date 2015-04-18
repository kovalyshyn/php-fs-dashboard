@extends('layouts.main')

@section('content')
  <div id="admin">

@if(Auth::check())

  <a class="btn btn-success" href="/getaways/callflow"><i class="icon-tasks icon-white"></i> Callflow</a>

@if($getaways->count() >0)

    <table class="table table-hover">
      <thead>
    <tr>
      <th>Created</th>
      <th>IP Addr</th>
      <th>CallerID</th>
      <th>CalleeID</th>
      <th>Channel</th>
      <th>State</th>
      <th>Codec</th>
    </tr>
  </thead>
    <tbody>
    @foreach ($getaways as $gw)
      @include('layouts.callflow', array('callflow'=>Channels::where('direction', 'LIKE', 'outbound')->where('name', 'LIKE', '%'.$gw->ip.':'.$gw->port)->get()))
    @endforeach
  </tbody>
  </table>
@endif


</div>

@endif

@stop

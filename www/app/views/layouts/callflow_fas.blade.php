@extends('layouts.main')

@section('content')
  <div id="admin">

@if(Auth::check())

  <a class="btn btn-success" href="/adm/fas/callflow"><i class="icon-tasks icon-white"></i> Callflow</a>


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
      @include('layouts.callflow', array('callflow'=>Channels::where('context', 'LIKE', 'fas')->get()))
  </tbody>
  </table>

</div>

@endif

@stop

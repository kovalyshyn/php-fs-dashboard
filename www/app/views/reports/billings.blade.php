@extends('layouts.main')

@section('content')
  <div id="admin">

    {{ Form::open(array('url'=>'reports/billings', 'class'=>'form-inline')) }}
    <input type="date" name="dateFrom" class="datepicker" data-date-format="dd/mm/yyyy" id="dateFrom" placeholder="date from"/> 
    <input type="date" name="dateTo" class="datepicker" data-date-format="dd/mm/yyyy" id="dateTo" placeholder="date to"/> 
    {{ Form::submit('Show', array('class'=>'btn')) }}
    {{ Form::close() }}

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

  </tbody>
  </table>

</div>

@stop
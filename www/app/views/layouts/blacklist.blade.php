@extends('layouts.numbers')

@section('blacklist')

<h4>{{ $dest->name }}</h4>

{{ Form::open(array('url'=>'blacklist/update', 'class'=>'form-signin')) }}
<hr />
Media <b>before Answer</b> are less then {{ Form::text('progress_before_answer', $dest->progress_before_answer, array('class'=>'input-mini')) }} seconds.<br /> 
Repeat {{ Form::text('repeat_calls', $dest->repeat_calls, array('class'=>'input-mini')) }} calls 
for last {{ Form::text('repeat_calls_minutes', $dest->repeat_calls_minutes, array('class'=>'input-mini')) }} minutes.
<hr />
Media <b>without Answer</b> are less then  {{ Form::text('progress_without_answer', $dest->progress_without_answer, array('class'=>'input-mini')) }} seconds. <br />
Repeat {{ Form::text('repeat_calls_without_answer', $dest->repeat_calls_without_answer, array('class'=>'input-mini')) }} calls 
for last {{ Form::text('repeat_calls_minutes_without_answer', $dest->repeat_calls_minutes_without_answer, array('class'=>'input-mini')) }} minutes.
<hr />
<label class="checkbox">{{ Form::checkbox('progress_no_answer', '1', $dest->progress_no_answer ) }} Progressing media <b>without Answer</b></label>
Repeat {{ Form::text('repeat_calls_no_answer', $dest->repeat_calls_no_answer, array('class'=>'input-mini')) }} calls 
for last {{ Form::text('repeat_calls_minutes_no_answer', $dest->repeat_calls_minutes_no_answer, array('class'=>'input-mini')) }} minutes.
<hr />
<label class="checkbox">{{ Form::checkbox('numA', '1', $dest->numA ) }} A Number to blacklist</label>
<label class="checkbox">{{ Form::checkbox('numB', '1', $dest->numB ) }} B Number to blacklist</label>
{{ Form::hidden('id', $dest->id) }}
{{ Form::submit('update', array('class'=>'btn btn-success btn-block')) }}
<p><i>0 = disabled<br/>
Answer = SIP 200 OK</i></p>
{{ Form::close() }}

@if($blacklist->count() > 0)
<a class="btn btn-danger" href="/blacklist/purge/{{ $dest->id }}"><i class="icon-trash icon-white"></i> Delete All</a> 
<a class="btn btn-info" href="/blacklist/csv/{{ $dest->id }}"><i class="icon-download-alt icon-white"></i> Export All</a> 
    {{ Form::open(array('url'=>'blacklist/modify')) }}
    <table class="table table-hover table-condensed">
      <thead>
    <tr>
      <th>Del</th>
      <th>Date</th>
      <th>A Number</th>
      <th>B Number</th>
      <th>Description</th>
    </tr>
  </thead>
    <tbody>
  @foreach ($blacklist as $bl)
      <tr><td>
      <input tabindex="1" type="checkbox" name="delIDs[{{$bl->id}}]" id="{{$bl->id}}" value="{{$bl->id}}">
      </td>
      <td>{{ $bl->added }}</td>
      <td>{{ $bl->caller_id_number }}</td>
      <td>{{ $bl->callee_id_number }}</td>
      <td>{{ $bl->description }}</td>
      </tr>
  @endforeach
  </tbody>
  </table>
  
  <?php echo $blacklist->links(); ?>

  {{ Form::hidden('dest_id', $dest->id) }}
  {{ Form::submit('Delete selected', array('class'=>'btn btn-danger')) }}
  {{ Form::close() }}
@endif

@stop

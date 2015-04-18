@extends('layouts.main')

@section('content')

@if(Auth::user()->type == '0')

  <h2>{{ $news->name }}</h2>

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

    {{ Form::open(array('url'=>'news/update', 'class'=>'form-signin')) }}
    {{ Form::text('name', $news->name, array('class'=>'input-block-level', 'placeholder'=>'Full Name')) }}
    {{ Form::textarea('the_news', $news->the_news) }}
    {{ Form::hidden('id', $news->id) }}
    {{ Form::submit('Update post', array('class'=>'btn btn-large btn-primary btn-block')) }}
    {{ Form::close() }}
    
    {{ Form::open(array('url'=>'news/destroy', 'class'=>'form-signin')) }}
    {{ Form::hidden('id', $news->id) }}
    {{ Form::submit('Delete post', array('class'=>'btn btn-large btn-danger btn-block')) }}
    {{ Form::close() }}

@endif

@stop
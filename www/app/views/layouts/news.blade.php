@extends('layouts.main')

@section('content')

@if(Auth::check())

  @foreach ($news as $msg)
      <h4>{{ $msg->name }}</h4>
      <small>{{ $msg->created_at }}</small>
      <blockquote>{{ Markdown::string($msg->the_news); }}</p>
      @if(Auth::user()->type == '0')
        {{ Form::open(array('url'=>'news/modify', 'class'=>'form-inline')) }}
        {{ Form::hidden('id', $msg->id) }}
        {{ Form::submit('modify post', array('class'=>'btn btn-mini btn-info')) }}
        {{ Form::close() }}
      @endif
      </blockquote>
      <hr class="container">
  @endforeach

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
  	{{ Form::open(array('url'=>'news/create', 'class'=>'form-signin')) }}
  	{{ Form::text('name', null, array('class'=>'input-block-level', 'placeholder'=>'The news title')) }}
  	{{ Form::textarea('the_news', null, array('class'=>'input-block-level')) }}
  	{{ Form::submit('Post the news', array('class'=>'btn btn-large btn-inverse btn-block')) }}
  	{{ Form::close() }}
@endif

@endif

@stop
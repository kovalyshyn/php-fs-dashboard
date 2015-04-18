@extends('layout')

@section('content')
  @foreach ($switchusers as $user)
    <p>{{ $user->name }}</p>
  @endforeach
@stop

@extends('layouts.master')


@section('title')
    View the book {{ $book->title }}
@endsection

@section('head')
    <link href="/css/books/show.css" type='text/css' rel='stylesheet'>
@endsection


@section('content')
    @if($book)
        <h1>Show book: {{ $book->title }}</h1>
    @else
        <h1>No book chosen</h1>
    @endif

    <form method='POST' action='/books/{{ $book->id }}'>
    {{ method_field('DELETE') }}
    {{ csrf_field() }}
    <button type="delete" class="btn btn-primary">Delete book</button>

@endsection


@section('body')
    <script src="/js/books/show.js"></script>
@endsection

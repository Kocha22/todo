@extends('layouts.template')

@section('content')
<section class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>Image By Id</h1>
            <p>{{ $post->image }}</p>
            <img id="preview" src="{{ asset('img/' . $post->image) }}" alt="your image" class="mt-3"/>
        </div>
    </div>
</section>
@endsection
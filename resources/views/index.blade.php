@extends('LaravelSaas::layouts.master')

@section('content')
<div class="container">
    <h1>Plugin: LaravelSaas</h1>

    <p>
        This view is loaded from plugin: {!! config('laravel-saas.name') !!}
    </p>

    <a href="{{ route('laravel-saas.setting') }}">Go to the LaravelSaas plugin settings page.</a>
</div>
@endsection

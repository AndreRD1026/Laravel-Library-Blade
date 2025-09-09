@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3"> Lista de Categorías </h1>

    <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3">➕ Nueva Categoría</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th> Nombre </th>
                <th> Descripción </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
            <tr>
                <td> {{ $category->name }} </td>
                <td> {{ $category->description }} </td>
            </tr>
                
            @endforeach
        </tbody>
    </table>
</div>
@endsection
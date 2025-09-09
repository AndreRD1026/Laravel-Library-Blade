@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">👨‍💼 Lista de Autores</h1>
    <a href="{{ route('authors.create') }}" class="btn btn-primary mb-3">➕ Nuevo Autor</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Fecha de Nacimiento</th>
                <th>Nacionalidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($authors as $author)
            <tr>
                <td>{{ $author->name }}</td>
                <td>{{ $author->birthdate ?? '-' }}</td>
                <td>{{ $author->nationality ?? '-' }}</td>
                <td>
                    <a href="{{ route('authors.edit', $author) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <form action="{{ route('authors.destroy', $author) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este autor?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

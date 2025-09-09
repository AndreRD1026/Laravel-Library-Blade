@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">📚 Lista de Libros</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary mb-3">➕ Nuevo Libro</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Año</th>
                <th>Precio</th>
                <th>Autor</th>
                <th>Categorías</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->published_year }}</td>
                <td>${{ number_format($book->price, 2) }}</td>
                <td>{{ $book->author->name }}</td>
                <td>
                    @foreach($book->categories as $cat)
                        <span class="badge bg-info text-dark">{{ $cat->name }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">✏️ Editar</a>
                    <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro?')">🗑️ Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

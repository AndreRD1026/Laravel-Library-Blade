@extends('layouts.app')

@section('content')
<div class="container">
    <h1>➕ Crear Libro</h1>

    <form action="{{ route('books.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="publication_year" class="form-label">Año de Publicación</label>
            <input type="number" name="published_year" class="form-control">
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Precio</label>
            <input type="number" step="0.01" name="price" class="form-control">
        </div>

        <div class="mb-3">
            <label for="author_id" class="form-label">Autor</label>
            <select name="author_id" class="form-control">
                @foreach($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="categories" class="form-label">Categorías</label>
            <select name="categories[]" class="form-control" multiple>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection

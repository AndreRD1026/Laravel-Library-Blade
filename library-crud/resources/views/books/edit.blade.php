@extends('layouts.app')

@section('content')
<div class="container">
    <h1>✏️ Editar Libro</h1>

    <form action="{{ route('books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}" required>
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="isbn" class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}" required>
            @error('isbn')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="publication_year" class="form-label">Año de Publicación</label>
            <input type="number" name="publication_year" class="form-control" value="{{ old('publication_year', $book->publication_year) }}">
            @error('publication_year')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Precio</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $book->price) }}">
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="author_id" class="form-label">Autor</label>
            <select name="author_id" class="form-control">
                @foreach($authors as $author)
                    <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>
            @error('author_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="categories" class="form-label">Categorías</label>
            <select name="categories[]" class="form-control" multiple>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', $book->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('categories')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success">💾 Actualizar</button>
        <a href="{{ route('books.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection

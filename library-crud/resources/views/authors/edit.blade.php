@extends('layouts.app')

@section('content')
<div class="container">
    <h1>✏️ Editar Autor</h1>

    <form action="{{ route('authors.update', $author) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" value="{{ $author->name }}" required>
        </div>

        <div class="mb-3">
            <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="birthdate" class="form-control" value="{{ $author->birthdate }}">
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">Nacionalidad</label>
            <input type="text" name="nationality" class="form-control" value="{{ $author->nationality }}">
        </div>

        <button class="btn btn-success">💾 Actualizar</button>
        <a href="{{ route('authors.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>➕ Crear Autor</h1>

    <form action="{{ route('authors.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nombre</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="birthdate" class="form-control">
        </div>

        <div class="mb-3">
            <label for="nationality" class="form-label">Nacionalidad</label>
            <input type="text" name="nationality" class="form-control">
        </div>

        <button class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('authors.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection

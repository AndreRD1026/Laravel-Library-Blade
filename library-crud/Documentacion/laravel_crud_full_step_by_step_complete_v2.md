
# Guía de uso — CRUD Librería en Laravel 12 + Blade (Paso a paso) 

---
## Índice
1. Crear proyecto
2. Configurar base de datos
3. Crear modelos, migraciones y controladores (paso a paso)
4. Editar migraciones (tablas)
5. Ejecutar migraciones
6. Definir relaciones en modelos
7. Crear controladores (Blade-ready) — código completo
8. (Opcional) Crear controladores API (JSON) — código completo
9. Registrar `routes/web.php` y `routes/api.php` + registro en `bootstrap/app.php`
10. Vistas Blade (layout, books, authors, categories) — archivos completos
11. Seeders de ejemplo
12. Levantar servidores y probar

---
## 1) Crear proyecto
```bash
laravel new library-crud
cd library-crud
```

---
## 2) Configurar base de datos 
Crear archivo sqlite:
```bash
touch database/database.sqlite
```
Editar `.env` y dejar:
```env
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
```

---
## 3) Crear modelos, migraciones y controladores (paso a paso)
Crear cada artefacto por separado para tener control total.

Modelos:
```bash
php artisan make:model Author
php artisan make:model Category
php artisan make:model Book
```

Migraciones:
```bash
php artisan make:migration create_authors_table
php artisan make:migration create_categories_table
php artisan make:migration create_books_table
php artisan make:migration create_book_category_table --create=book_category
```

Controladores resource (separados):
```bash
php artisan make:controller AuthorController --resource
php artisan make:controller CategoryController --resource
php artisan make:controller BookController --resource
```

---
## 4) Editar migraciones

**database/migrations/xxxx_create_authors_table.php**
```php
public function up(): void
{
    Schema::create('authors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->date('birthdate')->nullable();
        $table->string('nationality')->nullable();
        $table->timestamps();
    });
}
```

**database/migrations/xxxx_create_categories_table.php**
```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->timestamps();
    });
}
```

**database/migrations/xxxx_create_books_table.php**
```php
public function up(): void
{
    Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('isbn')->unique();
        $table->integer('published_year')->nullable();
        $table->decimal('price', 8, 2)->default(0);
        $table->integer('stock')->default(0);
        $table->foreignId('author_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

**database/migrations/xxxx_create_book_category_table.php**
```php
public function up(): void
{
    Schema::create('book_category', function (Blueprint $table) {
        $table->id();
        $table->foreignId('book_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

---
## 5) Ejecutar migraciones
```bash
php artisan migrate
```
Para reiniciar datos durante desarrollo:
```bash
php artisan migrate:fresh
```

---
## 6) Definir relaciones en modelos

**app/Models/Author.php**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name','birthdate','nationality'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
```

**app/Models/Category.php**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','description'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category');
    }
}
```

**app/Models/Book.php**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','isbn','published_year','price','stock','author_id'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }
}
```

---
## 7) Controladores Web para Blade

**app/Http/Controllers/BookController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['author','categories'])->get();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.create', compact('authors','categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'published_year' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'author_id' => 'required|exists:authors,id',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $book = Book::create($validated);

        if (!empty($validated['categories'])) {
            $book->categories()->sync($validated['categories']);
        }

        return redirect()->route('books.index')->with('success','Libro creado con éxito.');
    }

    public function show(Book $book)
    {
        $book->load(['author','categories']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        $book->load('categories');
        return view('books.edit', compact('book','authors','categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,'.$book->id,
            'published_year' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'stock' => 'nullable|integer',
            'author_id' => 'required|exists:authors,id',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $book->update($validated);

        if (isset($validated['categories'])) {
            $book->categories()->sync($validated['categories']);
        } else {
            $book->categories()->detach();
        }

        return redirect()->route('books.index')->with('success','Libro actualizado con éxito.');
    }

    public function destroy(Book $book)
    {
        $book->categories()->detach();
        $book->delete();
        return redirect()->route('books.index')->with('success','Libro eliminado con éxito.');
    }
}
```

**app/Http/Controllers/AuthorController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::all();
        return view('authors.index', compact('authors'));
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
        ]);

        Author::create($data);
        return redirect()->route('authors.index')->with('success','Autor creado con éxito.');
    }

    public function show(Author $author)
    {
        $author->load('books');
        return view('authors.show', compact('author'));
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string|max:255',
        ]);

        $author->update($data);
        return redirect()->route('authors.index')->with('success','Autor actualizado con éxito.');
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return redirect()->route('authors.index')->with('success','Autor eliminado con éxito.');
    }
}
```

**app/Http/Controllers/CategoryController.php**
```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($data);
        return redirect()->route('categories.index')->with('success','Categoría creada con éxito.');
    }

    public function show(Category $category)
    {
        $category->load('books');
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($data);
        return redirect()->route('categories.index')->with('success','Categoría actualizada con éxito.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success','Categoría eliminada con éxito.');
    }
}
```

---
## 8) (Prueba de API) Controladores API — devolver JSON
Si además quieres exponer una API, crea controladores para API o reutiliza controladores existentes con rutas `api.php`. Aquí hay controladores API sencillos (API namespace opcional).

**app/Http/Controllers/Api/BookApiController.php**
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function index() { return Book::with(['author','categories'])->get(); }
    public function store(Request $request) { /* validación similar a Web + retorna 201 */ }
    public function show(Book $book) { return $book->load(['author','categories']); }
    public function update(Request $request, Book $book) { /* validar y actualizar, regresar modelo */ }
    public function destroy(Book $book) { $book->delete(); return response()->noContent(); }
}
```
*(Implementar `store` y `update` con las mismas reglas de validación que en los controladores Web; ver ejemplos previos.)*

---
## 9) Rutas y registro `api.php` en Laravel 12

### 9.1 Crear `routes/api.php`
Crear `routes/api.php` con contenido:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookApiController;
use App\Http\Controllers\Api\AuthorApiController;
use App\Http\Controllers\Api\CategoryApiController;

Route::apiResource('books', BookApiController::class);
Route::apiResource('authors', AuthorApiController::class);
Route::apiResource('categories', CategoryApiController::class);
```

### 9.2 Registrar `api.php` en `bootstrap/app.php` (Laravel 12)
Ruta `bootstrap/app.php`, buscar la llamada `->withRouting(` e incluir `api: __DIR__.'/../routes/api.php',` como en este ejemplo:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    channels: __DIR__.'/../routes/channels.php',
)
```

Esto permite que `routes/api.php` sea cargado y sus rutas respondan bajo el middleware API por defecto.

---
## 10) Vistas Blade

Crear `resources/views/layouts/app.blade.php`:

```blade
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Library')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
  <div class="container">
    <a class="navbar-brand" href="{{ route('books.index') }}">📚 Librería</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('books.index') }}">Libros</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('authors.index') }}">Autores</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}">Categorías</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

**resources/views/books/index.blade.php**

**resources/views/books/create.blade.php** and **edit.blade.php**

**resources/views/authors/** and **resources/views/categories/**

---
## 11) Seeder de ejemplo (SampleDataSeeder)
Crear `database/seeders/SampleDataSeeder.php` y pegar:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $a1 = \App\Models\Author::create(['name'=>'Gabriel García Márquez','birthdate'=>'1927-03-06','nationality'=>'Colombiana']);
        $a2 = \App\Models\Author::create(['name'=>'Isabel Allende','birthdate'=>'1942-08-02','nationality'=>'Chilena']);

        $c1 = \App\Models\Category::create(['name'=>'Realismo Mágico']);
        $c2 = \App\Models\Category::create(['name'=>'Novela Histórica']);

        $b1 = \App\Models\Book::create(['title'=>'Cien Años de Soledad','isbn'=>'9780307474728','published_year'=>1967,'price'=>19.99,'stock'=>50,'author_id'=>$a1->id]);
        $b1->categories()->attach([$c1->id]);

        $b2 = \App\Models\Book::create(['title'=>'La Casa de los Espíritus','isbn'=>'9780553383805','published_year'=>1982,'price'=>14.99,'stock'=>30,'author_id'=>$a2->id]);
        $b2->categories()->attach([$c1->id,$c2->id]);
    }
}
```

Correr seed:
```bash
php artisan db:seed --class=SampleDataSeeder
```

---
## 12) Levantar servidores y probar
Levantar Laravel:
```bash
php artisan serve
```

Abrir en navegador:
- http://127.0.0.1:8000/books
- http://127.0.0.1:8000/authors
- http://127.0.0.1:8000/categories

Probar API (Postman, Insomnia):
- Se debe de enviar header `Accept: application/json`
- `GET /api/books`, `POST /api/books` con JSON, etc.

---

---
## Completado

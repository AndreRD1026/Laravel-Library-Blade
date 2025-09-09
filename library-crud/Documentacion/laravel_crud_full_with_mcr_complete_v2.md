
# Guía de uso con `-mcr` — CRUD Librería en Laravel 12 + Blade

---
## Índice
1. Crear proyecto
2. Configurar base de datos
3. Generar modelos+migration+controller con `-mcr`
4. Editar migraciones (tablas y pivote)
5. Ejecutar migraciones
6. Completar modelos (rellenar $fillable y relaciones)
7. Completar controladores generados (Blade-ready) — pegar código completo
8. (Opcional) Crear controladores API y registrar `routes/api.php`
9. Registrar `routes/web.php` y editar `bootstrap/app.php` para cargar `api.php`
10. Vistas Blade (layout, books, authors, categories) — archivos completos
11. Seeder de ejemplo
12. Levantar servidores y probar

---
## 1) Crear proyecto
```bash
laravel new library-crud
cd library-crud
```

Instalar dependencias JS si es necesario:
```bash
npm install
npm run dev
```

---
## 2) Configurar base de datos (SQLite)
```bash
touch database/database.sqlite
# .env
DB_CONNECTION=sqlite
DB_DATABASE=./database/database.sqlite
```

---
## 3) Generar modelos + migraciones + controladores con `-mcr`
Ejecutar los siguientes comandos (cada uno genera: modelo, migración y controlador resource):
```bash
php artisan make:model Author -mcr
php artisan make:model Category -mcr
php artisan make:model Book -mcr
```

- `-m` crea migración
- `-c` crea controlador
- `-r` crea controlador resource (métodos CRUD)

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

Crear migración pivote manual (si no fue generada):
```bash
php artisan make:migration create_book_category_table --create=book_category
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

---
## 6) Completar modelos (rellenar $fillable y relaciones)

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
## 7) Completar controladores generados (Blade-ready) 

Cuando generaste con `-mcr` Laravel ya creó `app/Http/Controllers/BookController.php`, etc. — normalmente estarán vacíos; reemplázalos por el contenido completo que sigue (idéntico al de la guía paso a paso).

**app/Http/Controllers/BookController.php**

**app/Http/Controllers/AuthorController.php** y **CategoryController.php**

---
## 8) (Para la versión 12) Controladores API y rutas `routes/api.php`

Para exponer API REST, crea controllers API (ubicación `App\Http\Controllers\Api`) y `routes/api.php`:
```bash
php artisan make:controller Api/BookApiController --api
php artisan make:controller Api/AuthorApiController --api
php artisan make:controller Api/CategoryApiController --api
```

Ejemplo rápido de `Api/BookApiController` (implementación mínima):
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function index() { return Book::with(['author','categories'])->get(); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'=>'required|string|max:255',
            'isbn'=>'required|string|unique:books,isbn',
            'published_year'=>'nullable|integer',
            'price'=>'nullable|numeric',
            'stock'=>'nullable|integer',
            'author_id'=>'required|exists:authors,id',
            'categories'=>'array',
            'categories.*'=>'exists:categories,id'
        ]);
        $book = Book::create($validated);
        if(!empty($validated['categories'])) $book->categories()->sync($validated['categories']);
        return response()->json($book->load(['author','categories']), 201);
    }

    public function show(Book $book) { return $book->load(['author','categories']); }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'=>'required|string|max:255',
            'isbn'=>'required|string|unique:books,isbn,'.$book->id,
            'published_year'=>'nullable|integer',
            'price'=>'nullable|numeric',
            'stock'=>'nullable|integer',
            'author_id'=>'required|exists:authors,id',
            'categories'=>'array',
            'categories.*'=>'exists:categories,id'
        ]);
        $book->update($validated);
        $book->categories()->sync($validated['categories'] ?? []);
        return response()->json($book->load(['author','categories']));
    }

    public function destroy(Book $book) { $book->delete(); return response()->noContent(); }
}
```

Crear `routes/api.php`:
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

---
## 9) Registrar `routes/api.php` en Laravel 12 (`bootstrap/app.php`)

En Laravel 12 el archivo `routes/api.php` no se crea registrado por defecto. Para cargarlo se edita `bootstrap/app.php` y en la llamada:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    channels: __DIR__.'/../routes/channels.php',
)
```

---
## 10) Vistas Blade (archivos completos)

**resources/views/layouts/app.blade.php** 
**resources/views/books/index.blade.php** 
**resources/views/books/create.blade.php** 
**resources/views/books/edit.blade.php** 
**resources/views/authors/** y **resources/views/categories/**
---
## 11) Seeder de ejemplo
(igual que guía paso a paso).

---
## 12) Levantar servidores y probar
```bash
php artisan serve
```

Probar rutas web: `http://127.0.0.1:8000/books`  
Probar API (Postman): `GET /api/books`, `POST /api/books` (headers `Accept: application/json`, `Content-Type: application/json`)

---
## Fin - Guía con `-mcr` (completa y autónoma)

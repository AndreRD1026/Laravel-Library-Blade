<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //? PARA USO DE API
        //return Book::with(['author', 'categories'])->get();
        
        //* PARA USO DE FRONTEND
        $books = Book::with(['author', 'categories'])->get();
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors = Author::all();
        $categories = Category::all();
        return view('books.create', compact('authors', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'title' => 'required|string',
        //     'isbn' => 'required|string|unique:books',
        //     'published_year' => 'required|integer',
        //     'price' => 'required|numeric',
        //     'stock' => 'required|integer',
        //     'author_id' => 'required|exists:authors,id',
        //     'categories' => 'array|exists:categories,id'
        // ]);

        // $book = Book::create($data);
        // if ($request->has('categories')) {
        //     $book->categories()->sync($data['categories']);
        // }

        // return $book->load(['author','categories']);

        //* PARA USO DE FRONTEND
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books,isbn',
                'published_year' => 'required|integer',
                'price' => 'nullable|numeric',
                'author_id' => 'required|exists:authors,id',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id',
            ]);

            $book = Book::create($validated);
            if ($request->has('categories')) {
                $book->categories()->attach($request->categories);
            }

            return redirect()->route('books.index')->with('success', 'Libro creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return $book->load(['author','categories']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $authors = Author::all();
        $categories = Category::all();
        $book->load('categories');

        return view('books.edit', compact('book', 'authors', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'title' => 'required|string',
        //     'isbn' => 'required|string|unique:books,isbn,'.$book->id,
        //     'published_year' => 'required|integer',
        //     'price' => 'required|numeric',
        //     'stock' => 'required|integer',
        //     'author_id' => 'required|exists:authors,id',
        //     'categories' => 'array|exists:categories,id'
        // ]);

        // $book->update($data);
        // if ($request->has('categories')) {
        //     $book->categories()->sync($data['categories']);
        // }
        // return $book->load(['author','categories']);

        //* PARA USO DE FRONTEND
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'publication_year' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'author_id' => 'required|exists:authors,id',
                'categories' => 'array',
                'categories.*' => 'exists:categories,id',
            ]);

            $book->update($validated);

            // Sincroniza categorías
            if ($request->has('categories')) {
                $book->categories()->sync($request->categories);
            } else {
                $book->categories()->detach(); // Si no hay categorías seleccionadas, se eliminan todas
            }

            return redirect()->route('books.index')->with('success', 'Libro actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        //? PARA USO DE API
        // $book->delete();
        // return response()->noContent();

        //* PARA USO DE FRONTEND
            $book->categories()->detach(); // eliminar relaciones pivot
            $book->delete();

            return redirect()->route('books.index')->with('success', 'Libro eliminado con éxito.');
    }
}

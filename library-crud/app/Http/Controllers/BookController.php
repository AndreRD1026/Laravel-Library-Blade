<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Book::with(['author', 'categories'])->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'published_year' => 'required|integer',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
            'categories' => 'array|exists:categories,id'
        ]);

        $book = Book::create($data);
        if ($request->has('categories')) {
            $book->categories()->sync($data['categories']);
        }

        return $book->load(['author','categories']);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn,'.$book->id,
            'published_year' => 'required|integer',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
            'categories' => 'array|exists:categories,id'
        ]);

        $book->update($data);
        if ($request->has('categories')) {
            $book->categories()->sync($data['categories']);
        }
        return $book->load(['author','categories']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->noContent();
    }
}

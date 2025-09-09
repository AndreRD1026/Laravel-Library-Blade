<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //? PARA USO DE API
        // return Author::all();

        //* PARA USO DE FRONTEND
        $authors = Author::all();
        return view('authors.index', compact('authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('authors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'name' => 'required|string',
        //     'birthdate' => 'nullable|date',
        //     'nationality' => 'nullable|string'
        // ]);
        // return Author::create($data);

        //* PARA USO DE FRONTEND
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'birthdate' => 'nullable|date',
                'nationality' => 'nullable|string|max:255'
            ]);

            Author::create($data);

            return redirect()->route('authors.index')->with('success', 'Autor creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        //? PARA USO DE API
        // return $author->load('books');

        //* PARA USO DE FRONTEND
        return view('authors.show', compact('author'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'name' => 'required|string',
        //     'birthdate' => 'nullable|date',
        //     'nationality' => 'nullable|string'
        // ]);

        // $author->update($data);
        // return $author;

        //* PARA USO DE FRONTEND
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'birthdate' => 'nullable|date',
                'nationality' => 'nullable|string|max:255'
            ]);

            $author->update($data);

            return redirect()->route('authors.index')->with('success', 'Autor actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        //? PARA USO DE API
        // $author->delete();
        // return response()->noContent();

        //* PARA USO DE FRONTEND
            $author->delete();
            return redirect()->route('authors.index')->with('success', 'Autor eliminado con éxito.');
    }
}

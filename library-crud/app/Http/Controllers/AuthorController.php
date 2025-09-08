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
        return Author::all();
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
            'name' => 'required|string',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string'
        ]);
        return Author::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Author $author)
    {
        return $author->load('books');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'birthdate' => 'nullable|date',
            'nationality' => 'nullable|string'
        ]);

        $author->update($data);
        return $author;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author)
    {
        $author->delete();
        return response()->noContent();
    }
}

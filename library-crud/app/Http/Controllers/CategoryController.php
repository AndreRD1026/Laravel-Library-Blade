<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //? PARA USO DE API
        // return Category::all();

        //* PARA USO DE FRONTEND
        $categories = Category::all();
        return view('categories.index', compact("categories"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'name' => 'required|string',
        //     'description' => 'nullable|string'
        // ]);
        // return Category::create($data);

        //* PARA USO DE FRONTEND
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success','Categoría creada con éxito');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //? PARA USO DE API
        // return $category->load('books');

        //* PARA USO DE FRONTEND
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //? PARA USO DE API
        // $data = $request->validate([
        //     'name' => 'required|string',
        //     'description' => 'nullable|string'
        // ]);

        // $category->update($data);
        // return $category;

        //* PARA USO DE FRONTEND
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255'
        ]);

        $category->update($data);
        return redirect()->route('categories.index')->with('success','Categoría actualizada con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //? PARA USO DE API
        // $category->delete();
        // return response()->noContent();

        //* PARA USO DE FRONTEND
        $category->delete();
        return redirect()->route('categories.index')->with('success','Categoría eliminada con éxito');
    }
}

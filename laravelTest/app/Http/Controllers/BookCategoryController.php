<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\BookCategory;

class BookCategoryController extends Controller
{
    public function showCreateForm()
    {
        $categories = BookCategory::all();
        return view('bookCategory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = BookCategory::create([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function bookCategoryCreate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:book_categories|max:255',
        ]);

        $category = BookCategory::create($validatedData);

        if ($request->ajax()) {
            return response()->json(['success' => 'Kategori buku berhasil ditambahkan!', 'category' => $category]);
        }

        return back()->with('success', 'Kategori buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $category = BookCategory::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($category);
        }

        return view('bookCategory.create', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = BookCategory::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|unique:book_categories,name,' . $category->id . '|max:255',
        ]);

        $category->update($validatedData);

        if ($request->ajax()) {
            return response()->json(['success' => 'Kategori buku berhasil diperbarui!', 'category' => $category]);
        }

        return redirect()->route('bookCategory.create')->with('success', 'Kategori buku berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Kategori buku berhasil dihapus!', 'id' => $id]);
        }

        return redirect()->route('bookCategory.create')->with('success', 'Kategori buku berhasil dihapus!');
    }
}





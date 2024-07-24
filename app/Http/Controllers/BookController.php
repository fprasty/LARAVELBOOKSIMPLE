<?php

namespace App\Http\Controllers;

use App\Models\BooksTable;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{

    // Handle category filter
public function filter(Request $request)
{
    $categoryId = $request->get('category_id');

    if ($categoryId) {
        $books = BooksTable::where('category_id', $categoryId)->with('category')->get();
    } else {
        $books = BooksTable::with('category')->get();
    }

    // Map image URLs
    $books->map(function ($book) {
        $book->image = $book->image ? Storage::url($book->image) : null;
        return $book;
    });

    return response()->json(['books' => $books]);
}
    // Menampilkan daftar buku
    public function index(Request $request)
    {
        // Ambil ID kategori dari request
        $categoryId = $request->input('category_id');

        // Filter buku berdasarkan kategori jika ada
        $books = BooksTable::with('category')
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->get();

        // Ambil semua kategori buku
        $categories = BookCategory::all();

        // Mengembalikan view dengan data buku dan kategori
        if ($request->ajax()) {
            return response()->json([
                'books' => view('books._list', compact('books'))->render(),
            ]);
        }

        return view('dashboard', compact('books', 'categories'));
    }

    // Menyimpan buku baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:book_categories,id',
            'user_id' => 'required|exists:users,id'
        ]);
    
        $book = new BooksTable();
        $book->title = $validatedData['title'];
        $book->description = $validatedData['description'];
        $book->category_id = $validatedData['category_id'];
        $book->user_id = $validatedData['user_id'];
    
        if ($request->hasFile('image')) {
            $book->image = $request->file('image')->store('public/images');
        }
    
        $book->save();
    
        if ($request->ajax()) {
            return response()->json([
                'success' => 'Buku berhasil ditambahkan!',
                'book' => $book,
                'image_url' => $book->image ? Storage::url($book->image) : null,
                'category' => $book->category ? $book->category->name : 'No Category'
            ]);
        }
    
        return redirect()->route('dashboard')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Menampilkan form edit buku
    public function edit($id)
    {
        $book = BooksTable::findOrFail($id);
        $categories = BookCategory::all(); // Ambil semua kategori buku untuk form

        if (request()->ajax()) {
            return response()->json([
                'book' => $book,
                'categories' => $categories,
                'image_url' => $book->image ? Storage::url($book->image) : null
            ]);
        }

        return view('books.edit', compact('book', 'categories'));
    }

    // Mengupdate buku
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:book_categories,id',
            'image' => 'nullable|image'
        ]);
    
        $book = BooksTable::findOrFail($id);
        $book->title = $request->input('title');
        $book->description = $request->input('description');
        $book->category_id = $request->input('category_id');
    
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books', 'public');
            $book->image = $imagePath;
        }
    
        $book->save();
    
        $book->load('category');
    
        return response()->json([
            'success' => true,
            'book' => $book,
            'image_url' => $book->image ? Storage::url($book->image) : 'https://via.placeholder.com/150',
            'category' => $book->category ? $book->category->name : 'No Category'
        ]);
    }

    // Menghapus buku
    public function destroy(Request $request, $id)
    {
        $book = BooksTable::findOrFail($id);

        if ($book->image && Storage::exists($book->image)) {
            Storage::delete($book->image);
        }

        $book->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Buku berhasil dihapus!',
                'id' => $id
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Buku berhasil dihapus!');
    }
}
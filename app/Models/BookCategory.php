<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    protected $table = 'book_categories'; // Nama tabel jika tidak mengikuti konvensi Laravel
    protected $fillable = ['name']; // Kolom yang dapat diisi

    // Definisikan relasi dengan model Book
    public function books()
    {
        return $this->hasMany(BooksTable::class, 'category_id');
    }
}

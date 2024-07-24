<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BooksTable extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image', // Kolom untuk nama file gambar buku
        'category_id', // Kolom untuk ID kategori buku
        'user_id', // Kolom untuk ID pengguna (user)
    ];

    // Definisikan relasi dengan model BookCategory
    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    // Definisikan relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

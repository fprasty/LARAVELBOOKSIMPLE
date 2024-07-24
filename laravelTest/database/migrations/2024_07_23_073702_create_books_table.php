<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books_tables', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_id'); // Kolom untuk ID user
            $table->unsignedBigInteger('category_id'); // Kolom untuk ID kategori buku
            $table->timestamps();

             // Definisikan relasi dengan tabel kategori buku
             $table->foreign('category_id')->references('id')->on('book_categories');
             $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_table');
    }
};

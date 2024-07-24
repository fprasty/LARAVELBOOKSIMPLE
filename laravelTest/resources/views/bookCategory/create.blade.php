@extends('master')

@section('content')
<div class="container mt-5">
    <a href="{{ route('dashboard') }}" class="btn btn-primary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    <h1>{{ isset($category) ? '' : '' }} Book Category</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form id="categoryForm" method="POST" action="{{ isset($category) ? route('bookCategory.update', $category->id) : route('bookCategory.store') }}">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', isset($category) ? $category->name : '') }}" required maxlength="255">
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Create' }}</button>
    </form>

    @if(isset($category))
        <form id="deleteForm" method="POST" action="{{ route('bookCategory.destroy', $category->id) }}" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    @endif

    <h2 class="mt-5">Book Categories</h2>
    <table class="table" id="categoryTable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr id="category-{{ $category->id }}">
                    <th scope="row">{{ $category->id }}</th>
                    <td>{{ $category->name }}</td>
                    <td>
                        <button class="btn btn-warning edit-button" data-id="{{ $category->id }}">Edit</button>
                        <button class="btn btn-danger delete-button" data-id="{{ $category->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Menambahkan token CSRF ke header
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // AJAX untuk pengiriman form
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah form disubmit secara tradisional

        let form = $(this);
        let formData = form.serialize();
        let actionUrl = form.attr('action');
        let method = form.find('input[name="_method"]').val() || 'POST';

        $.ajax({
            type: method,
            url: actionUrl,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.category) {
                    let newCategoryHtml = `
                        <tr id="category-${response.category.id}">
                            <th scope="row">${response.category.id}</th>
                            <td>${response.category.name}</td>
                            <td>
                                <button class="btn btn-warning edit-button" data-id="${response.category.id}">Edit</button>
                                <button class="btn btn-danger delete-button" data-id="${response.category.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    
                    // Jika mengupdate, ganti row yang ada
                    if (method === 'PUT') {
                        $(`#category-${response.category.id}`).replaceWith(newCategoryHtml);
                    } else {
                        // Jika membuat baru, tambahkan di akhir tabel
                        $('#categoryTable tbody').append(newCategoryHtml);
                    }
                    
                    // Reset form
                    $('#categoryForm')[0].reset();
                    $('button[type="submit"]').text('Create');
                    $('#categoryForm').attr('action', '{{ route('bookCategory.store') }}');
                    $('#categoryForm').find('input[name="_method"]').remove();
                    alert(response.success);
                } else {
                    alert('Data kategori tidak ditemukan.');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });

     // AJAX untuk tombol hapus
     $('#categoryTable').on('click', '.delete-button', function(e) {
        e.preventDefault();

        if (!confirm('Are you sure you want to delete this category?')) {
            return;
        }

        let button = $(this);
        let categoryId = button.data('id');
        let deleteUrl = `/book-category/${categoryId}/delete`;

        $.ajax({
            type: 'DELETE',
            url: deleteUrl,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.success);
                $(`#category-${categoryId}`).remove(); // Hapus baris yang dihapus
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });

    // AJAX untuk tombol edit
    $('#categoryTable').on('click', '.edit-button', function(e) {
        e.preventDefault();

        let button = $(this);
        let categoryId = button.data('id');
        let editUrl = `/book-category/${categoryId}/edit`;

        $.ajax({
            type: 'GET',
            url: editUrl,
            success: function(response) {
                $('#name').val(response.name);
                $('#categoryForm').attr('action', `/book-category/${categoryId}/update`);
                
                // Tambahkan input hidden untuk metode PUT
                if ($('#categoryForm').find('input[name="_method"]').length === 0) {
                    $('#categoryForm').prepend('<input type="hidden" name="_method" value="PUT">');
                }
                
                $('button[type="submit"]').text('Update');
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + xhr.responseText);
            }
        });
    });
});
</script>
@endsection

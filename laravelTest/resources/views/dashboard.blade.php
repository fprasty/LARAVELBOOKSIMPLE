@extends('master')

@section('content')
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">Laravel 11 Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarColor01">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link text-white"> Welcome: {{ ucfirst(Auth()->user()->name) }} </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('logout') }}"> Logout </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">

    <!-- Add Data Buku Section -->
    <div class="mb-4">
            <h2>Isi Data Buku Dibawah</h2>
        </div>

        <!-- Form to Add New Book -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form id="bookForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() }}">
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </form>
            </div>
        </div>

        <!-- Filter and List of Books -->
        <div class="row mb-4">
            <div class="col-md-9">
                <div class="form-group">
                    <label for="filter_category_id">Filter by Category</label>
                    <select class="form-control" id="filter_category_id">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ route('bookCategory.create') }}" class="btn btn-success">Add Category</a>
            </div>
        </div>

        <div class="row g-4" id="bookList">
            @if ($books->isEmpty())
                <div class="col-12">
                    <p>No books available.</p>
                </div>
            @else
                @foreach ($books as $book)
                    <div class="col-md-3 mb-4 d-flex align-items-stretch" id="book-{{ $book->id }}">
                        <div class="card w-100 h-100">
                            <img src="{{ $book->image ? Storage::url($book->image) : 'https://via.placeholder.com/150' }}" class="card-img-top" alt="Book Image">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $book->title }}</h5>
                                <p class="card-text">{{ $book->description }}</p>
                                <p>Category: {{ $book->category->name }}</p>
                                <div class="mt-auto">
                                    <button class="btn btn-warning btn-edit" data-id="{{ $book->id }}">Edit</button>
                                    <button class="btn btn-danger btn-delete" data-id="{{ $book->id }}">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" role="dialog" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editBookForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_book_id" name="id">
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_image">Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image">
                        </div>
                        <div class="form-group">
                            <label for="edit_category_id">Category</label>
                            <select class="form-control" id="edit_category_id" name="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        // Handle form submission for adding new book
        $('#bookForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route('books.store') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        let book = response.book;
                        let image_url = response.image_url ? response.image_url : '';
                        $('#bookList').append(
                            `<div class="col-md-3 mb-4" id="book-${book.id}">
                                <div class="card">
                                    <img src="${image_url ? image_url : 'https://via.placeholder.com/150'}" class="card-img-top" alt="Book Image">
                                    <div class="card-body">
                                        <h5 class="card-title">${book.title || 'No Title'}</h5>
                                        <p class="card-text">${book.description || 'No Description'}</p>
                                        <p>Category: ${book.category ? book.category.name : 'No Category'}</p>
                                        <button class="btn btn-warning btn-edit" data-id="${book.id}">Edit</button>
                                        <button class="btn btn-danger btn-delete" data-id="${book.id}">Delete</button>
                                    </div>
                                </div>
                            </div>`
                        );
                        $('#bookForm')[0].reset();
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        });

        // Handle edit button click
        $(document).on('click', '.btn-edit', function() {
            let bookId = $(this).data('id');
            $.ajax({
                url: `{{ url('books') }}/${bookId}/edit`,
                type: 'GET',
                success: function(response) {
                    if (response.book) {
                        $('#edit_book_id').val(response.book.id);
                        $('#edit_title').val(response.book.title);
                        $('#edit_description').val(response.book.description);
                        $('#edit_category_id').val(response.book.category_id);
                        $('#editBookModal').modal('show');
                    }
                },
                error: function(xhr) {
                    console.error('An error occurred:', xhr.responseText);
                }
            });
        });

   // Handle form submission for editing book
$('#editBookForm').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append('_method', 'PUT'); // Add _method field if using method PUT

    let bookId = $('#edit_book_id').val();

    $.ajax({
        url: `{{ url('books') }}/${bookId}`,
        type: 'POST', // Use POST but include _method to simulate PUT
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Update UI with new data
                let book = response.book;
                let image_url = response.image_url ? response.image_url : '';
                
                $(`#book-${book.id}`).html(
                    `<div class="card">
                        <img src="${image_url ? image_url : 'https://via.placeholder.com/150'}" class="card-img-top" alt="Book Image">
                        <div class="card-body">
                            <h5 class="card-title">${book.title || 'No Title'}</h5>
                            <p class="card-text">${book.description || 'No Description'}</p>
                            <p>Category: ${book.category ? book.category.name : 'No Category'}</p>
                            <button class="btn btn-warning btn-edit" data-id="${book.id}">Edit</button>
                            <button class="btn btn-danger btn-delete" data-id="${book.id}">Delete</button>
                        </div>
                    </div>`
                );
                
                $('#editBookModal').modal('hide'); // Hide the modal after successful update
            } else {
                console.error('Update failed:', response);
            }
        },
        error: function(xhr) {
            console.error('An error occurred:', xhr.responseText);
        }
    });
});

        // Handle delete button click
        $(document).on('click', '.btn-delete', function() {
            let bookId = $(this).data('id');
            if (confirm('Are you sure you want to delete this book?')) {
                $.ajax({
                    url: `{{ url('books') }}/${bookId}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            $(`#book-${bookId}`).remove();
                        }
                    },
                    error: function(xhr) {
                        console.error('An error occurred:', xhr.responseText);
                    }
                });
            }
        });

      // Handle category filter
$('#filter_category_id').on('change', function() {
    let categoryId = $(this).val();
    $.ajax({
        url: '{{ route('books.filter') }}',
        type: 'GET',
        data: { category_id: categoryId },
        success: function(response) {
            let books = response.books;
            $('#bookList').empty();
            if (books.length > 0) {
                books.forEach(book => {
                    let image_url = book.image ? book.image : '';
                    $('#bookList').append(
                        `<div class="col-md-3 mb-4" id="book-${book.id}">
                            <div class="card">
                                <img src="${image_url ? image_url : 'https://via.placeholder.com/150'}" class="card-img-top" alt="Book Image">
                                <div class="card-body">
                                    <h5 class="card-title">${book.title || 'No Title'}</h5>
                                    <p class="card-text">${book.description || 'No Description'}</p>
                                    <p>Category: ${book.category ? book.category.name : 'No Category'}</p>
                                    <button class="btn btn-warning btn-edit" data-id="${book.id}">Edit</button>
                                    <button class="btn btn-danger btn-delete" data-id="${book.id}">Delete</button>
                                </div>
                            </div>
                        </div>`
                    );
                });
            } else {
                $('#bookList').append('<p>No books available.</p>');
            }
        },
        error: function(xhr) {
            console.error('An error occurred:', xhr.responseText);
        }
    });
});
    });
</script>
@endsection

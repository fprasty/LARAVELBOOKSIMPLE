<div class="row d-flex align-items-stretch">
    @if ($books->isEmpty())
        <div class="col-12">
            <p>No books available.</p>
        </div>
    @else
        @foreach ($books as $book)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100" id="book-{{ $book->id }}">
                    @if ($book->image)
                        <img src="{{ Storage::url($book->image) }}" class="card-img-top" alt="Book Image">
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text">{{ $book->description }}</p>
                        <p class="card-text"><small class="text-muted">Category: {{ $book->category->name }}</small></p>
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
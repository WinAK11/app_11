@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>All Ebooks</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">All Ebooks</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search" method="GET" action="{{ route('admin.ebooks') }}">
                            <fieldset class="name">
                                <input type="text" placeholder="Search ebooks..." name="title"
                                    value="{{ request('title') }}">
                            </fieldset>
                            <div class="button-submit">
                                <button type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                    <a class="tf-button style-1 w208" href="{{ route('admin.ebook.add') }}">
                        <i class="icon-plus"></i>Add new
                    </a>
                </div>

                <div class="table-responsive">
                    @if (Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center">#</th>
                                <th style="width: 100px; text-align: center">Cover</th>
                                <th style="width: 150px; text-align: center">Title</th>
                                <th style="width: 120px; text-align: center">Author</th>
                                <th style="width: 120px; text-align: center">Category</th>
                                <th style="width: 80px; text-align: center">Format</th>
                                <th class="text-center">Description</th>
                                <th style="width: 120px; text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($ebooks as $ebook)
                                <tr>
                                    <td class="text-center">{{ $ebook->id }}</td>
                                    <td class="text-center">
                                        @if ($ebook->cover_path)
                                            <img src="{{ asset($ebook->cover_path) }}" alt="cover" width="60">
                                        @else
                                            <span class="text-muted">No cover</span>
                                        @endif
                                    </td>
                                    <td>{{ $ebook->title }}</td>
                                    <td>{{ $ebook->author }}</td>
                                    <td class="text-center">{{ $ebook->category->name }}</td>
                                    <td class="text-center">{{ strtoupper($ebook->format) }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($ebook->description, 150) }}</td>
                                    <td>
                                        <div class="list-icon-function">
                                            <a href="{{ asset($ebook->file_path) }}" target="_blank">
                                                <div class="item eye" title="Download/View">
                                                    <i class="icon-download"></i>
                                                </div>
                                            </a>
                                            <a href="{{ route('admin.ebook.edit', ['id' => $ebook->id]) }}">
                                                <div class="item edit">
                                                    <i class="icon-edit-3"></i>
                                                </div>
                                            </a>
                                            <form action="{{ route('admin.ebook.delete', ['id' => $ebook->id]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-0 item text-danger delete"
                                                    style="border:none; background:none;">
                                                    <i class="icon-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $ebooks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                swal({
                    title: "Are you sure?",
                    text: 'You want to delete this ebook?',
                    icon: "warning",
                    buttons: ["No", "Yes"],
                    dangerMode: true
                }).then(function(result) {
                    if (result) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

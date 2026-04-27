@extends('admin.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-0">All Users</h4>
    </div>

    <div class="card-body">

        {{-- Filter + Search --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 mb-3">

            {{-- Filter --}}
            <div class="col-md-4">
                <select name="filter" class="form-control">
                    <option value="">-- Filter Users --</option>
                    <option value="active" {{ request('filter') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('filter') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="col-md-5">
                <input type="text" name="search" placeholder="Search by email"
                       value="{{ request('search') }}" class="form-control">
            </div>

            {{-- Buttons --}}
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-50">Apply</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-50">Reset</a>
            </div>
        </form>


        {{-- Users Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        {{-- <th>Registered</th> --}}
                        <th>Name</th>
                        <th>Email</th>
                        <th>status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>

                        <td>{{ $user->name }}</td>

                        <td>{{ $user->email }}</td>

                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td>
                            <button type="button" class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#userModal{{ $user->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- VIEW / EDIT USER MODAL -->
                    <div class="modal fade" id="userModal{{ $user->id }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5>User Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-2">
                                            <label>Name</label>
                                            <input type="text" name="name"
                                                value="{{ $user->name }}"
                                                class="form-control">
                                        </div>

                                        <div class="mb-2">
                                            <label>Email</label>
                                            <input type="email" name="email"
                                                value="{{ $user->email }}"
                                                class="form-control">
                                        </div>

                                        <div class="mb-2">
                                            <label>Status</label>
                                            <select name="is_active" class="form-control">
                                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-success w-100">
                                            Update User
                                        </button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="4" class="text-center py-3">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $users->withQueryString()->links('admin.layouts.partials.__pagination') }}
        </div>
    </div>

    {{-- SweetAlert --}}
    @if(session('success'))
    <script>
        Swal.fire({
            icon: "success",
            title: "Success!",
            text: "{{ session('success') }}",
            timer: 2500,
            showConfirmButton: false
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "{{ session('error') }}",
            timer: 2500,
            showConfirmButton: false
        });
    </script>
    @endif
</div>
@endsection
